#!/usr/bin/env python3
import re

# Lire le fichier
with open('resources/js/Components/POS/ContactLensSearchModal.vue', 'r', encoding='utf-8') as f:
    content = f.read()

# Corriger les problèmes de balises mixtes
# Remplacer <input avec des </select> par <select
content = re.sub(
    r'<input(\s+[^>]*v-model="form\.o[dg]\.(color|material)"[^>]*)>\s*(<option[^>]*>.*?</select>)',
    r'<select\1>\3',
    content,
    flags=re.DOTALL
)

# Corriger les autres problèmes similaires
content = re.sub(
    r'<input(\s+[^>]*v-model="form\.(duration|boxSize)"[^>]*)>\s*(<option[^>]*>.*?</select>)',
    r'<select\1>\3',
    content,
    flags=re.DOTALL
)

# Écrire le fichier corrigé
with open('resources/js/Components/POS/ContactLensSearchModal.vue', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fichier corrigé avec succès !")
