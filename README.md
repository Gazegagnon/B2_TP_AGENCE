# LocAuto Pro — Plateforme de location de véhicules

Application web **full PHP** (sans framework) pour une agence de location automobile : catalogue, réservations, espace client, back-office administrateur et messagerie. Projet portfolio présentant une architecture **MVC maison**, la séparation des rôles et un **workflow de réservation** (validation en agence ou paiement en ligne simulé).

---

## En bref (pitch recruteur)

LocAuto Pro permet à un **client** de parcourir le parc, de réserver un véhicule sur des créneaux de dates, de suivre l’état de sa location depuis son compte et d’échanger avec le service client. Les **administrateurs** gèrent les utilisateurs, le parc, les agences, les avis, les réservations récentes et une messagerie interne. Le code met l’accent sur la **lisibilité**, les **requêtes préparées**, la **gestion de session** et une **couche modèle** capable de s’adapter à deux schémas SQL de réservation (cours vs projet).

---

## Stack technique

| Élément | Détail |
|--------|--------|
| Langage | **PHP** (orienté objet, namespaces implicites par autoload) |
| Base de données | **MySQL** / **MariaDB** via **PDO** |
| Front | **HTML5**, **Bootstrap 5.3**, **Bootstrap Icons**, polices **Google Fonts** |
| Serveur cible | **Apache** (ex. **XAMPP** sur Windows) |
| Architecture | **MVC** + **routeur** (front controller léger) |
| Authentification | **Sessions PHP** + contrôle des rôles (`CLIENT`, `ADMIN`, `COMMERCIAL`) |

---

## Fonctionnalités principales

### Espace public et client

- **Accueil** avec mise en avant du catalogue et filtres (parcours type vitrine).
- **Catalogue public** : liste des véhicules disponibles à la consultation.
- **Fiche véhicule** : détails, suggestions du même type, **avis clients** (commentaires modérés côté admin).
- **Réservation** : choix des dates, enregistrement lié au compte utilisateur.
- **Compte client** : profil, liste des réservations avec **statuts visuels** (en attente, confirmée ligne/agence, en cours, terminée, annulée), montant estimé, agence de retrait, actions d’annulation selon les règles métier.
- **Workflow réservation** (si la migration SQL est appliquée) :
  - Réservation créée en **« en attente »**.
  - Le client peut **finaliser en ligne** via un **paiement sécurisé simulé** (démo) → statut **confirmée — paiement en ligne**.
  - Alternative : validation par un **administrateur depuis le tableau de bord** → **confirmée — agence**.
  - **Suivi** : rafraîchissement automatique périodique de la page compte lorsque la réservation est encore « à suivre » (effet temps réel léger, sans WebSocket).
- **Messagerie client** : fil de discussion avec l’équipe (selon tables présentes en base).

### Espace administrateur

- **Tableau de bord** : statistiques (utilisateurs, véhicules, agences, réservations, estimation de revenus du mois), activité récente, actions rapides.
- **Gestion des clients** et **équipe admin** (inscription admin, mises à jour profil, suppressions sécurisées).
- **Modération des commentaires** sur les véhicules.
- **Notifications** internes (ex. nouvelle réservation, messages).
- **Messagerie** avec les clients.
- **Suivi du parc** : statuts véhicule (selon schéma SQL étendu `admin_module_schema.sql`).
- **Parc par type** : voitures, motos, camions — CRUD selon les droits.

### Rôle commercial

- Point d’entrée **dashboard commercial** (écran dédié, extensible selon les besoins métier).

### Sécurité et bonnes pratiques

- **Jeton CSRF** en session pour les formulaires sensibles (annulation, réservation, messagerie, actions admin).
- **Contrôle d’accès** : redirections si non connecté ou mauvais rôle.
- **Requêtes préparées** centralisées dans les modèles (`AbstractModel::executerReq`).
- **Flash messages** après actions (succès / erreur) pour une UX claire.
- Garde sur les chemins des **vues** pour éviter les inclusions arbitraires.

---

## Structure du dépôt

```
B2_TP_AGENCE/
├── index.php              # Point d’entrée → charge le routeur
├── routeur.php            # Routage par ?action=..., autoload, session
├── assets/                # CSS / JS applicatifs (si présents)
├── classes/               # Entités métier (User, Vehicule, Reservation, Commentaire…)
├── controller/            # Contrôleurs (UserController, VehiculeController…)
├── model/                 # Accès données PDO (XxxModel)
├── views/                 # Templates PHP (pages + admin + partials)
└── CONCEPTION/            # Scripts SQL, dumps et évolutions de schéma
```

**Flux typique** : `index.php` → `routeur.php` → contrôleur selon `action` → `AbstractController::render()` → `views/template.php` + vue concernée.

---

## Prérequis

- **PHP 8.x** recommandé (syntaxe moderne sur certaines vues/contrôleurs, ex. `match`).
- **MySQL / MariaDB**.
- **Apache** avec module rewrite optionnel (l’app s’appuie sur `index.php?action=...`).
- **Extension PDO MySQL** activée.

---

## Installation locale (ex. XAMPP)

1. **Cloner ou copier** le dossier du projet dans le répertoire web, par exemple :
   `C:\xampp\htdocs\B2_TP_AGENCE`

2. **Créer la base de données** `b2_tp_agence` (ou adapter le nom dans `model/AbstractModel.php`).

3. **Importer le schéma** : utiliser en priorité les scripts présents dans `CONCEPTION/`, typiquement :
   - un dump de référence du cours ou du projet (`b2_tp_agence (3).sql`, `bd.sql`, etc.) ;
   - puis, si besoin, les **migrations** dans un ordre cohérent :
     - réservation / tables manquantes (`create_table_reservation.sql`) ;
     - **workflow location** (`location_workflow.sql`) — colonnes `statut`, `paiement_en_ligne`, `updated_at` sur `reservation` ;
     - module admin parc (`admin_module_schema.sql`) ;
     - autres correctifs du dossier `CONCEPTION/` selon votre environnement.

4. **Configurer PDO** dans `model/AbstractModel.php` :
   - hôte (`127.0.0.1`) ;
   - nom de la base ;
   - utilisateur / mot de passe MySQL (souvent `root` / vide en local XAMPP).

5. Démarrer **Apache** et **MySQL**, puis ouvrir dans le navigateur :
   `http://localhost/B2_TP_AGENCE/`  
   (ou le chemin équivalent selon votre `DocumentRoot`).

> **Note** : le script `location_workflow.sql` contient un `UPDATE` de rattrapage pour les anciennes réservations. Il est pensé pour être exécuté **une seule fois** juste après l’`ALTER` ; ne pas le relancer en production sur une base déjà migrée sans adaptation, sous peine de réécraser des statuts `en_attente` légitimes.

---

## Paramètres d’URL (routing)

L’application utilise une **query string unique** : `?action=nom_de_l_action`.

Exemples :

- `?action=home` — accueil  
- `?action=catalogue_public` — catalogue vitrine  
- `?action=connexion` / `?action=compte` — authentification / espace client  
- `?action=admin_dashboard` — tableau de bord administrateur  

La liste complète des actions est déclarée dans `routeur.php` et répartie entre les contrôleurs.

---

## Modèle de données (vue d’ensemble)

Entités typiques (noms exacts selon scripts SQL) :

- **personne** — utilisateurs (login, rôle, etc.)
- **vehicule** — parc (marque, modèle, prix journalier, type, lien **agence**, éventuellement image / statut parc)
- **agence** — points de retrait
- **reservation** ou **reserver** — selon version du schéma ; le code du `ReservationModel` **détecte** la table utilisée et la présence des colonnes de **workflow**
- **commentaire** — avis sur les véhicules
- tables optionnelles : **message_interne**, **notification_admin**, etc.

---

## Points forts à valoriser en entretien

1. **MVC sans framework** : compréhension du pattern plutôt que de la seule configuration d’un outil tiers.  
2. **Compatibilité schéma** : le modèle réservation s’adapte (`reserver` vs `reservation` + colonnes optionnelles).  
3. **Workflow métier** : états de réservation, annulation conditionnelle, distinction validation **en ligne** vs **en agence**.  
4. **UX admin + client** : tableaux de bord, fil d’activité, messages flash.  
5. **Sécurité de base** : CSRF, sessions, rôles, PDO préparé.

---

## Défis techniques résolus

- **Double schéma de réservation** — Le cours utilise parfois la table `reserver`, le projet la table `reservation` avec des colonnes supplémentaires. Plutôt que dupliquer la logique, `ReservationModel` détecte la table et les colonnes présentes (`hasReservationWorkflow()`, requêtes conditionnelles) pour garder un seul code métier.
- **Workflow location sans perdre l’historique** — Les annulations passent en statut `annulee` (UPDATE) plutôt qu’en suppression, ce qui alimente l’historique client et évite de fausser les statistiques admin. La migration SQL (`location_workflow.sql`) est documentée pour ne pas être ré-exécutée à tort sur une base déjà en production.
- **Parcours client / admin alignés** — Même réservation, deux chemins de confirmation (paiement en ligne simulé côté client, bouton « Valider agence » côté admin), avec phases d’affichage calculées à partir des dates (en cours, terminée) même si le statut SQL reste « confirmé ».
- **Modèle commentaire hétérogène** — Adaptation du modèle et des entités aux colonnes réellement présentes en base (`id_personne` / `contenu`, types PDO en chaîne) pour éviter les erreurs d’hydratation sans casser les écrans existants.
- **Sécurisation des actions sensibles** — Jeton CSRF sur les POST (réservation, annulation, finalisation paiement, messagerie, actions admin) et contrôle systématique des rôles avant rendu des vues back-office.

---

## Limites assumées

- Pas de framework (pas de Symfony/Laravel) : moins de « batteries incluses », plus de code maison à maintenir.  
- **Paiement en ligne** : simulation démo uniquement, pas d’intégration Stripe/PayPal.  
- **Temps réel** : rafraîchissement HTTP, pas de push/WebSocket.  
- Environnement pensé pour **XAMPP / développement** ; déploiement production nécessiterait durcissement (HTTPS, config PDO externalisée, désactivation de `display_errors`, etc.).

---

## Auteur et contexte

Projet **B2** — travaux pratiques / agence de location — démontrant compétences en **PHP objet**, **MySQL**, **intégration HTML/CSS**, et conception d’une **application web transactionnelle** simple mais complète.

---

## Licence

Usage **portfolio** sauf mention contraire du dépôt.
