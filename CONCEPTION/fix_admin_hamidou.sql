-- Corrige le compte admin : mdp en bcrypt + rôle ADMIN (comme attendu par le PHP)
UPDATE personne
SET
  mdp = '$2y$10$HQs0wOg83clKMEpNwzWeS.JHDLtavrTkAyLeBzCWTrhHLUVAdT9We',
  role = 'ADMIN'
WHERE login = 'Hamidou';
