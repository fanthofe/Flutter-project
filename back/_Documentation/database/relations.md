User }o--|| Availability : Chaque utilisateur peut être associé à aucune ou plusieurs disponibilités et chaque disponibilité appartient à un seul utilisateur. Type de relation : OneToMany

User  }o--o{ Chat : Chaque utilisateur peut être associé à aucune ou plusieurs discussions, et chaque discussion peut impliquer plusieurs utilisateurs. Type de relation : ManyToMany

Chat }o--|| ChatMessage : Chaque discussion peut être associée à aucun ou plusieurs messages, et chaque message appartient à une discussion. Type de relation : OneToMany

User }o--|| Comment : Chaque utilisateur peut être associé à aucun ou plusieurs commentaires, et chaque commentaire est associé à un utilisateur. Type de relation : OneToMany

User }o--|| Children : Chaque utilisateur peut être associé à aucun ou plusieurs enfants, et chaque enfant appartient à un seul utilisateur. Type de relation : OneToMany

Order ||-o{ Subscription : Chaque facture est associée à un abonnement, et un abonnement peut être associé à plusieurs factures. Type de relation : ManyToOne

User }o--|| Order : Chaque utilisateur peut avoir aucune ou plusieurs factures et chaque facture est liée à un utilisateur. Type de relation : OneToMany
