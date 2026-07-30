from fastapi import FastAPI, UploadFile, File, Form
from pdf2image import convert_from_bytes
from PIL import Image
import pytesseract
import re
import io
import json
import time

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
# OUTIL COMMUN : EXTRACTION DE TEXTE (OCR)
# =================================================

def extraire_texte_document(contenu: bytes, filename: str, content_type: str) -> str:
    """
    Extrait le texte brut d'un PDF ou d'une image via OCR.
    Réutilisé par /ocr et /analyser-document pour éviter la duplication.
    """

    texte = ""

    est_pdf = (
        content_type == "application/pdf"
        or filename.lower().endswith(".pdf")
    )

    if est_pdf:

        t_convert = time.time()

        # dpi=150 au lieu du défaut (souvent 200-300) : suffisant pour l'OCR
        # et nettement plus rapide sur les PDF multi-pages.
        images = convert_from_bytes(
            contenu,
            poppler_path=POPPLER_PATH,
            dpi=150
        )

        print(f"    [OCR] Conversion PDF -> images : {time.time() - t_convert:.1f}s "
              f"({len(images)} page(s))")

        for i, image in enumerate(images):

            t_page = time.time()

            texte += pytesseract.image_to_string(
                image,
                lang="fra+eng"
            )

            texte += "\n"

            print(f"    [OCR] Page {i + 1}/{len(images)} : {time.time() - t_page:.1f}s")

    else:

        image = Image.open(io.BytesIO(contenu))

        texte = pytesseract.image_to_string(
            image,
            lang="fra+eng"
        )

    return texte.replace("\r", "")


# =================================================
# OCR
# =================================================

@app.post("/ocr")
async def ocr(file: UploadFile = File(...)):

    contenu = await file.read()

    texte = extraire_texte_document(
        contenu,
        file.filename,
        file.content_type
    )

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
        # Nombre par table (facture / tva / virement / versement /
        # email / rappel / notification)
        # ------------------------------------

        elif "combien" in question and "facture" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM invoices")
            ).scalar()

            reponse = f"Il y a {total} facture(s) enregistrée(s)."

        elif "combien" in question and "virement" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM virements")
            ).scalar()

            reponse = f"Il y a {total} virement(s) enregistré(s)."

        elif "combien" in question and "versement" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM versements")
            ).scalar()

            reponse = f"Il y a {total} versement(s) enregistré(s)."

        elif "combien" in question and "email" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM emails")
            ).scalar()

            reponse = f"Il y a {total} email(s) enregistré(s)."

        elif "combien" in question and "rappel" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM rappels")
            ).scalar()

            reponse = f"Il y a {total} rappel(s) enregistré(s)."

        elif "combien" in question and "notification" in question:

            total = conn.execute(
                text("SELECT COUNT(*) FROM notifications")
            ).scalar()

            reponse = f"Il y a {total} notification(s) enregistrée(s)."

        # ------------------------------------
        # Nombre par marque (fallback générique pour "combien")
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
                "dans", "avec", "cette", "ces", "cet",
                "liste", "listez", "listes", "toutes", "toute", "tous", "tout",
                "affiche", "affichez", "afficher", "montre", "montrez", "montrer",
                "voir", "donne", "donnez", "donner", "peux", "peut", "veux",
                "voudrais", "avoir", "moi", "toi"
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
        # TVA
        # ------------------------------------

        elif "tva" in question:

            lignes = conn.execute(text("""
                SELECT numero, montant_ht, tva, montant_ttc
                FROM invoices
                ORDER BY date_facture DESC
            """)).fetchall()

            if lignes:

                txt = []

                for l in lignes:
                    txt.append(
                        f"{l.numero} : HT {l.montant_ht} DH - TVA {l.tva} DH - TTC {l.montant_ttc} DH"
                    )

                reponse = "\n".join(txt)

            else:
                reponse = "Aucune TVA trouvée."

        # ------------------------------------
        # VIREMENTS
        # ------------------------------------

        elif "virement" in question:

            lignes = conn.execute(text("""
                SELECT numero, beneficiaire, montant, statut, date_virement
                FROM virements
            """)).fetchall()

            if lignes:

                txt = []

                for l in lignes:
                    txt.append(
                        f"{l.numero} - {l.beneficiaire} - {l.montant} DH - {l.statut}"
                    )

                reponse = "\n".join(txt)

            else:
                reponse = "Aucun virement."

        # ------------------------------------
        # VERSEMENTS
        # ------------------------------------

        elif "versement" in question:

            lignes = conn.execute(text("""
                SELECT reference, client, montant, date_versement
                FROM versements
            """)).fetchall()

            if lignes:

                txt = []

                for l in lignes:
                    txt.append(
                        f"{l.reference} - {l.client} - {l.montant} DH"
                    )

                reponse = "\n".join(txt)

            else:
                reponse = "Aucun versement."

        # ------------------------------------
        # EMAILS
        # ------------------------------------

        elif "email" in question:

            lignes = conn.execute(text("""
                SELECT destinataire, objet, date_generation
                FROM emails
            """)).fetchall()

            if lignes:

                txt = []

                for l in lignes:
                    txt.append(
                        f"{l.destinataire} - {l.objet}"
                    )

                reponse = "\n".join(txt)

            else:
                reponse = "Aucun email."

        # ------------------------------------
        # RAPPELS
        # ------------------------------------

        elif "rappel" in question:

            lignes = conn.execute(text("""
                SELECT titre, date_rappel, termine
                FROM rappels
                ORDER BY date_rappel
            """)).fetchall()

            if lignes:

                txt = []

                for l in lignes:
                    etat = "Terminé" if l.termine else "En attente"

                    txt.append(
                        f"{l.titre} - {l.date_rappel} - {etat}"
                    )

                reponse = "\n".join(txt)

            else:
                reponse = "Aucun rappel."

        # ------------------------------------
        # NOTIFICATIONS
        # ------------------------------------

        elif "notification" in question:

            lignes = conn.execute(text("""
                SELECT message, created_at
                FROM notifications
            """)).fetchall()

            if lignes:
                reponse = "\n".join([l.message for l in lignes])
            else:
                reponse = "Aucune notification."

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
        # Aucune règle trouvée -> Gemma
        # ------------------------------------

        else:

            contexte = ""

            docs = conn.execute(text("""
                SELECT nom_proprietaire, marque, modele, immatriculation
                FROM documents
            """)).fetchall()

            factures = conn.execute(text("""
                SELECT numero, client, objet, montant_ttc
                FROM invoices
            """)).fetchall()

            for d in docs:
                contexte += f"""
Propriétaire : {d.nom_proprietaire}
Marque : {d.marque}
Modèle : {d.modele}
Immatriculation : {d.immatriculation}

"""

            for f in factures:
                contexte += f"""
Facture {f.numero}
Client : {f.client}
Objet : {f.objet}
Montant : {f.montant_ttc} DH

"""

            reponse = demander_gemma(question, contexte)

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


# =================================================
# ANALYSE DE DOCUMENT (OCR + EXTRACTION JSON via GEMMA)
# =================================================

def extraire_champs_via_gemma(texte: str, type_document: str = None) -> dict:
    """
    Envoie le texte OCR à Gemma et récupère un JSON structuré
    avec le type de document et les champs extraits (facture, TVA, etc.).
    """

    consigne_type = (
        f'Le type de document est déjà connu : "{type_document}". '
        f'Recopie-le tel quel dans le champ type_document.'
        if type_document else
        'Détecte le type de document parmi EXACTEMENT ces valeurs : '
        '"Titre de propriété", "Facture", "Reçu", "Virement", "Autre".'
    )

    prompt = f"""
Tu es CentrePilot AI. Tu extrais les informations importantes d'un document administratif
ou d'une facture pour un centre de contrôle technique automobile.

{consigne_type}

Voici le texte extrait par OCR (il peut contenir des erreurs de reconnaissance) :

{texte[:4000]}

Extrait les champs suivants s'ils sont présents. Si un champ est absent ou incertain,
mets sa valeur à null. N'invente JAMAIS d'information absente du texte.

Réponds STRICTEMENT avec un objet JSON valide, sans aucun texte avant ou après,
sans balises markdown, au format exact suivant :

{{
  "type_document": "...",
  "categorie": "...",
  "titre": "...",
  "champs": {{
    "numero_facture": null,
    "date": null,
    "client": null,
    "fournisseur": null,
    "montant_ht": null,
    "taux_tva": null,
    "montant_tva": null,
    "montant_ttc": null,
    "mode_paiement": null,
    "reference": null
  }}
}}
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

    brut = response["message"]["content"].strip()

    # Gemma ajoute parfois ```json ... ``` autour du JSON : on nettoie
    if brut.startswith("```"):
        brut = brut.strip("`")
        if brut.lower().startswith("json"):
            brut = brut[4:].strip()

    data = None

    try:
        data = json.loads(brut)
    except json.JSONDecodeError:

        # Filet de sécurité : on isole la première { et la dernière }
        debut = brut.find("{")
        fin = brut.rfind("}")

        if debut != -1 and fin != -1:
            try:
                data = json.loads(brut[debut:fin + 1])
            except json.JSONDecodeError:
                data = None

    if not isinstance(data, dict):
        data = {
            "type_document": type_document or "Autre",
            "categorie": "",
            "titre": "",
            "champs": {}
        }

    # Garantit la présence des clés attendues même si Gemma en oublie
    data.setdefault("type_document", type_document or "Autre")
    data.setdefault("categorie", "")
    data.setdefault("titre", "")
    data.setdefault("champs", {})

    if type_document:
        data["type_document"] = type_document

    return data


@app.post("/analyser-document")
async def analyser_document(
    file: UploadFile = File(...),
    type_document: str = Form(None)
):

    t_debut = time.time()

    contenu = await file.read()

    t0 = time.time()
    texte = extraire_texte_document(
        contenu,
        file.filename,
        file.content_type
    )
    print(f"⏱ OCR total : {time.time() - t0:.1f}s")

    print("========== ANALYSE DOCUMENT (OCR) ==========")
    print(texte)
    print("==============================================")

    t1 = time.time()
    resultat = extraire_champs_via_gemma(texte, type_document)
    print(f"⏱ Gemma (extraction) : {time.time() - t1:.1f}s")

    print(f"⏱ TOTAL /analyser-document : {time.time() - t_debut:.1f}s")

    resultat["texte"] = texte

    return resultat