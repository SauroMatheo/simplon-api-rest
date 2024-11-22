# Brief Simplon 1 - Création d'une API simple avec Symfony

## Équipes

**URL**: /api/equipes

### Récupérer toutes les équipes

**Méthode**: GET
**Paramètres**: Aucun
**Corps**: Aucun
**Réponse**: Une liste de toutes les équipes, avec les informations minimales

### Récupérer une équipe

**Méthode**: GET
**Paramètres**:

- `id` (Integer): L'identifiant de l'équipe

**Corps**: Aucun
**Réponse**: Renvoie les détails d'une équipe

### Supprimer une équipe

**Méthode**: DELETE
**Paramètres**:

- `id` (Integer): L'identifiant de l'équipe à supprimer

**Corps**: Aucun
**Réponse**: Aucun corps, Statut 204 (No Content)

### Créer une équipe

**Méthode**: POST
**Paramètres**: Aucun
**Corps**:

_(Exemple)_

```JSON
{
	"nom": "Nom de l'équipe",
	"score": 0
}
```

**Réponse**: Renvoie l'équipe créée, Statut 201 (Created)

### Modifier une équipe

**Méthode**: PUT
**Paramètres**: Aucun
**Corps**: { `id` (Integer): L'identifiant de l'équipe à modifier, `nom` (String, Facultatif), `score` (Integer, Facultatif) }

_(Exemple)_

```JSON
{
	"id": 1,
	"nom": "Nom de l'équipe",
	"score": 0
}
```

**Réponse**: Renvoie les détails d'une équipe

## Joueurs

**URL**: /api/joueurs

### Récupérer tous les joueurs

**Méthode**: GET
**Paramètres**: Aucun
**Corps**: Aucun
**Réponse**: Une liste de tous les joueurs, avec les informations minimales

_(Exemple)_

```JSON
[
	{
		"id": 1,
		"nom": "Doe",
		"prenom": "John",
		"equipe": {
			"id": 1
		}
	},
	{
		"id": 2,
		"nom": "Valjean",
		"prenom": "Jean",
		"equipe": null
	}
]
```

### Récupérer un joueur

**Méthode**: GET
**Paramètres**:

- `id` (Integer): L'identifiant du joueur

**Corps**: Aucun
**Réponse**: Renvoie les détails d'un joueur

_(Exemple)_

```JSON
{
	"id": 1,
	"nom": "Doe",
	"prenom": "John",
	"equipe": {
		"id": 1
	}
}
```

### Supprimer un joueur

**Méthode**: DELETE
**Paramètres**:

- `id` (Integer): L'identifiant du joueur à supprimer

**Corps**: Aucun
**Réponse**: Aucun corps, Statut 204 (No Content)

### Créer un joueur

**Méthode**: POST
**Paramètres**: Aucun
**Corps**: { `nom` (String), `prenom` (String), `equipe` (Integer, Facultatif) }

_(Exemple)_

```JSON
{
	"nom": "Doe",
	"prenom": "John",
	"equipe": 1
}
```

**Réponse**: Renvoie l'équipe créée, Statut 201 (Created)

### Modifier une équipe:

**Méthode**: PUT
**Paramètres**: Aucun
**Corps**: { `id` (Integer): L'identifiant de l'équipe à modifier, `nom` (String, Facultatif), `prenom` (String, Facultatif), `equipe` (Integer, Facultatif) }

Mettre `equipe` à `"null"` (String) retire l'équipe.

_(Exemple)_

```JSON
{
	"id": 1,
	"nom": "Doe",
	"prenom": "John",
	"equipe": 2
}
```

**Réponse**: Renvoie les détails d'une équipe
