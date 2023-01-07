# StsAnalytics

## TODO 

fonctionnalités :
    - analyser un run et afficher le récap façon STS
    D'abord en invite de commande + texte
        - lire un fichier placé dans un répertoire à déterminer
        - Afficher "métadonnéees" : 
            - perso joué
            - niveau d'ascension
            - victoire/défaite
            - score
            - seed
        - Afficher reliques trouvées
        - Faire un récap étage par étage :
            - Type d'étage ('?', monstre, coffre)
            - Etage effectif (un monstre dans une salle '?')
            - gold
            - hp courants et max
            si combat ;
                - ennemis
                - nb tours
                - hp perdus
                - cartes proposées + choix
            Si boss : 
                - infos "combat"
            Si Relique de boss :
                - Relique choisie
                - Relique ignorées
            Si élite :
                - infos "combat"
                - relique + choix
            Si shop :
                reliques achetées
                cartes achetées
                carte purgée
            Si event :
                "event_name": "World of Goop",
                "damage_healed": 0,
                "gold_gain": 75,
                "player_choice": "Gather Gold",
                "damage_taken": 11,
                "max_hp_gain": 0,
                "max_hp_loss": 0,
                "gold_loss": 0
                + infos combat si applicable

