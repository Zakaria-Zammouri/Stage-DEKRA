from fastapi import FastAPI, UploadFile, File
from pdf2image import convert_from_bytes
from PIL import Image
import pytesseract
import re
import io

from pydantic import BaseModel
from sqlalchemy import create_engine, text
import ollama

# -------------------------------------------------
# Configuration OCR
# -------------------------------------------------

pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

POPPLER_PATH = r"C:\poppler-26.02.0\Library\bin"

# -------------------------------------------------
# Base de données
# -------------------------------------------------

DATABASE_URL = "mysql+pymysql://root:@127.0.0.1:3307/dekra_centrepilot_ai"

engine = create_engine(DATABASE_URL)

# -------------------------------------------------
# FastAPI
# -------------------------------------------------

app = FastAPI(title="CentrePilot AI")

# =================================================
# ACCUEIL
# =================================================

@app.get("/")
def home():
    return {
        "message": "API CentrePilot AI"
    }

# =================================================
# OCR
# =================================================

@app.post("/ocr")
async def ocr(file: UploadFile = File(...)):

    contenu = await file.read()

    texte = ""

    est_pdf = (
        file.content_type == "application/pdf"
        or file.filename.lower().endswith(".pdf")
    )

    if est_pdf:

        images = convert_from_bytes(
            contenu,
            poppler_path=POPPLER_PATH
        )

        for image in images:

            texte += pytesseract.image_to_string(
                image,
                lang="fra+eng"
            )

            texte += "\n"

    else:

        image = Image.open(io.BytesIO(contenu))

        texte = pytesseract.image_to_string(
            image,
            lang="fra+eng"
        )

    texte = texte.replace("\r", "")

    print("============== OCR ==============")
    print(texte)
    print("=================================")

    nom = ""
    immatriculation = ""
    marque = ""
    modele = ""

    m = re.search(
        r"Nom\s+du\s+propri[ée]taire\s+(.+)",
        texte,
        re.IGNORECASE
    )

    if m:
        nom = m.group(1).strip()

    m = re.search(
        r"Num[ée]ro\s+d['’]immatriculation\s+(.+)",
        texte,
        re.IGNORECASE
    )

    if m:
        immatriculation = m.group(1).strip()

    m = re.search(
        r"Marque\s+(.+)",
        texte,
        re.IGNORECASE
    )

    if m:
        marque = m.group(1).strip()

    m = re.search(
        r"Mod[èée]le\s+(.+)",
        texte,
        re.IGNORECASE
    )

    if m:
        modele = m.group(1).strip()

    print("Nom :", nom)
    print("Immatriculation :", immatriculation)
    print("Marque :", marque)
    print("Modele :", modele)

    return {
        "nom_proprietaire": nom,
        "numero_immatriculation": immatriculation,
        "marque": marque,
        "modele": modele,
        "texte": texte
    }

# =================================================
# ASSISTANT IA
# =================================================

def demander_gemma(question, contexte):

    prompt = f"""
Tu es CentrePilot AI.

Tu aides les utilisateurs à consulter leurs documents.

Voici les données disponibles :

{contexte}

Question :

{question}

Réponds uniquement avec les informations présentes dans les données.
Si tu ne trouves rien, réponds :
"Je n'ai trouvé aucune information."
"""

    response = ollama.chat(
        model="gemma3",
        messages=[
            {
                "role": "user",
                "content": prompt
            }
        ]
    )

    return response["message"]["content"]

class Question(BaseModel):
    question: str


@app.post("/assistant")
def assistant(data: Question):

    question = data.question.lower().strip()
    print(">>> QUESTION REÇUE :", question)

    reponse = "Je n'ai trouvé aucune information."

    with engine.connect() as conn:

        # ------------------------------------
        # Nombre de documents
        # ------------------------------------

        if "combien" in question and "document" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM documents")
            ).scalar()

            reponse = f"Il y a {total} documents enregistrés."

        # ------------------------------------
        # Nombre de véhicules
        # ------------------------------------

        elif "combien" in question and "véhicule" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM documents")
            ).scalar()

            reponse = f"Il y a {total} véhicules enregistrés."

        # ------------------------------------
        # Nombre par marque
        # ------------------------------------

        elif "combien" in question:

            lignes = conn.execute(text("""
                SELECT marque,
                       COUNT(*) AS total
                FROM documents
                GROUP BY marque
            """)).fetchall()

            trouve = False

            for l in lignes:

                if l.marque and l.marque.lower() in question:

                    reponse = f"Il y a {l.total} véhicule(s) {l.marque}."

                    trouve = True

                    break

            if not trouve:
                reponse = "Je n'ai trouvé aucune marque correspondant à votre question."

        # ------------------------------------
        # Recherche de factures en langage naturel
        # ------------------------------------

        elif "facture" in question:

            mois_fr = {
                "janvier": 1, "février": 2, "fevrier": 2, "mars": 3, "avril": 4,
                "mai": 5, "juin": 6, "juillet": 7, "août": 8, "aout": 8,
                "septembre": 9, "octobre": 10, "novembre": 11,
                "décembre": 12, "decembre": 12
            }

            mois_trouve = None
            for nom_mois, numero in mois_fr.items():
                if nom_mois in question:
                    mois_trouve = numero
                    break

            mots_a_ignorer = [
                "facture", "factures", "mois", "quelles", "quels", "quelle", "quel",
                "sont", "les", "des", "une", "est", "que", "qui", "pour",
                "dans", "avec", "cette", "ces", "cet"
            ]

            mots = [
                m for m in question.split()
                if len(m) > 3 and m not in mois_fr and m not in mots_a_ignorer
            ]

            sql = "SELECT numero, client, objet, montant_ttc, statut, date_facture FROM invoices WHERE 1=1"
            params = {}

            if mots:
                conditions = []
                for i, mot in enumerate(mots):
                    conditions.append(f"(client LIKE :mot{i} OR objet LIKE :mot{i})")
                    params[f"mot{i}"] = f"%{mot}%"
                sql += " AND (" + " OR ".join(conditions) + ")"

            if mois_trouve:
                sql += " AND MONTH(date_facture) = :mois"
                params["mois"] = mois_trouve

            lignes = conn.execute(text(sql), params).fetchall()

            if lignes:

                txt = []

                for l in lignes:
                    txt.append(
                        f"Facture {l.numero} — {l.client} — {l.objet} — "
                        f"{l.montant_ttc} DH — {l.statut} — "
                        f"{l.date_facture}"
                    )

                reponse = "\n".join(txt)

            else:
                reponse = "Aucune facture ne correspond à votre recherche."

        # ------------------------------------
        # Tous les propriétaires
        # ------------------------------------

        elif "propriétaire" in question or "proprietaire" in question:

            lignes = conn.execute(text("""
                SELECT
                    nom_proprietaire,
                    marque,
                    modele,
                    immatriculation
                FROM documents
                ORDER BY nom_proprietaire
            """)).fetchall()

            txt = []

            for l in lignes:

                txt.append(
                    f"{l.nom_proprietaire} | {l.marque} {l.modele} | {l.immatriculation}"
                )

            reponse = "\n".join(txt)

        # ------------------------------------
        # Toutes les marques
        # ------------------------------------

        elif "marque" in question:

            lignes = conn.execute(text("""
                SELECT DISTINCT marque
                FROM documents
                WHERE marque IS NOT NULL
                ORDER BY marque
            """)).fetchall()

            reponse = "\n".join([l.marque for l in lignes])

        # ------------------------------------
        # Tous les modèles
        # ------------------------------------

        elif "modèle" in question or "modele" in question:

            lignes = conn.execute(text("""
                SELECT DISTINCT modele
                FROM documents
                WHERE modele IS NOT NULL
                ORDER BY modele
            """)).fetchall()

            reponse = "\n".join([l.modele for l in lignes])

        # ------------------------------------
        # Toutes les immatriculations
        # ------------------------------------

        elif "immatriculation" in question:

            lignes = conn.execute(text("""
                SELECT immatriculation
                FROM documents
                WHERE immatriculation IS NOT NULL
                ORDER BY immatriculation
            """)).fetchall()

            reponse = "\n".join([l.immatriculation for l in lignes])

        # ------------------------------------
        # Recherche intelligente
        # ------------------------------------

        else:

            lignes = conn.execute(text("""
                SELECT
                    nom_proprietaire,
                    marque,
                    modele,
                    immatriculation
                FROM documents
            """)).fetchall()

            resultat = []

            for l in lignes:

                nom = (l.nom_proprietaire or "").lower()
                marque = (l.marque or "").lower()
                modele = (l.modele or "").lower()
                imm = (l.immatriculation or "").lower()

                if (
                    question in nom
                    or question in marque
                    or question in modele
                    or question in imm
                ):

                    resultat.append(
                        f" Propriétaire : {l.nom_proprietaire}\n"
                        f" Marque : {l.marque}\n"
                        f" Modèle : {l.modele}\n"
                        f" Immatriculation : {l.immatriculation}"
                    )

            if resultat:

                reponse = "\n\n".join(resultat)

    return {
        "question": data.question,
        "reponse": reponse
    }


# =================================================
# ASSISTANT IA (GEMMA)
# =================================================

@app.post("/assistant-ia")
def assistant_ia(data: Question):

    with engine.connect() as conn:

        lignes = conn.execute(text("""
            SELECT
                nom_proprietaire,
                marque,
                modele,
                immatriculation
            FROM documents
        """)).fetchall()

        factures = conn.execute(text("""
            SELECT
                numero,
                client,
                objet,
                montant_ttc,
                statut,
                date_facture
            FROM invoices
        """)).fetchall()

    contexte = "=== VÉHICULES / DOCUMENTS ===\n"

    for l in lignes:

        contexte += (
            f"Propriétaire : {l.nom_proprietaire}\n"
            f"Marque : {l.marque}\n"
            f"Modèle : {l.modele}\n"
            f"Immatriculation : {l.immatriculation}\n\n"
        )

    contexte += "\n=== FACTURES ===\n"

    for f in factures:

        contexte += (
            f"Facture {f.numero} | Client : {f.client} | Objet : {f.objet} | "
            f"Montant TTC : {f.montant_ttc} DH | Statut : {f.statut} | "
            f"Date : {f.date_facture}\n"
        )

    reponse = demander_gemma(
        data.question,
        contexte
    )

    return {
        "question": data.question,
        "reponse": reponse
    }


# =================================================
# GÉNÉRATION DE MAIL PAR IA
# =================================================

class GenerationMail(BaseModel):
    type_modele: str
    destinataire: str = ""
    contexte: str = ""


@app.post("/generer-mail")
def generer_mail(data: GenerationMail):

    prompt = f"""
Tu es l'assistant administratif du centre de contrôle technique DEKRA CentrePilot AI.

Rédige un email professionnel et courtois en français pour la situation suivante : {data.type_modele}.

Informations du dossier concerné :
{data.contexte if data.contexte else "Aucune information supplémentaire fournie."}

Réponds UNIQUEMENT selon ce format exact, sans rien ajouter d'autre avant ou après :
OBJET: <objet du mail>
CORPS: <corps complet du mail, signé "Contrôle Automobile et Expertise — DEKRA">
"""

    response = ollama.chat(
        model="gemma3",
        messages=[
            {
                "role": "user",
                "content": prompt
            }
        ]
    )

    texte = response["message"]["content"]

    objet = ""
    corps = ""

    if "OBJET:" in texte and "CORPS:" in texte:
        objet = texte.split("OBJET:")[1].split("CORPS:")[0].strip()
        corps = texte.split("CORPS:")[1].strip()
    else:
        corps = texte.strip()

    return {
        "objet": objet,
        "corps": corps
    }


# =================================================
# CLASSEMENT AUTOMATIQUE DES DOCUMENTS
# =================================================

class ClassificationRequest(BaseModel):
    texte: str


@app.post("/classifier-document")
def classifier_document(data: ClassificationRequest):

    prompt = f"""
Tu es un assistant qui classe des documents administratifs pour un centre de contrôle technique automobile.

Voici le texte extrait par OCR d'un document :

{data.texte[:3000]}

Détermine :
1. Le type du document, parmi EXACTEMENT ces valeurs : "Titre de propriété", "Facture", "Reçu", "Virement", "Autre"
2. Une catégorie courte et pertinente (2-3 mots maximum)
3. Un titre normalisé et court pour ce document (5-8 mots maximum)

Réponds UNIQUEMENT selon ce format exact, sans rien ajouter d'autre :
TYPE: <type>
CATEGORIE: <catégorie>
TITRE: <titre>
"""

    response = ollama.chat(
        model="gemma3",
        messages=[
            {
                "role": "user",
                "content": prompt
            }
        ]
    )

    texte_reponse = response["message"]["content"]

    type_detecte = "Autre"
    categorie = ""
    titre = ""

    if "TYPE:" in texte_reponse and "CATEGORIE:" in texte_reponse:
        type_detecte = texte_reponse.split("TYPE:")[1].split("CATEGORIE:")[0].strip()

    if "CATEGORIE:" in texte_reponse and "TITRE:" in texte_reponse:
        categorie = texte_reponse.split("CATEGORIE:")[1].split("TITRE:")[0].strip()

    if "TITRE:" in texte_reponse:
        titre = texte_reponse.split("TITRE:")[1].strip()

    types_valides = ["Titre de propriété", "Facture", "Reçu", "Virement", "Autre"]

    if type_detecte not in types_valides:
        type_detecte = "Autre"

    return {
        "type": type_detecte,
        "categorie": categorie,
        "titre": titre
    }