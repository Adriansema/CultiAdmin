### Gestión de Contraseñas Seguras

**Objetivo:** Asegurar que las contraseñas de usuario sean robustas y consistentes tanto en la validación del frontend como del backend, y que se manejen de forma segura en las transacciones HTTP.

**Criterios de Robustez:**
Las contraseñas deben tener un mínimo de 8 caracteres y contener al menos una letra minúscula, una letra mayúscula, un número y un símbolo.

**Conjunto de Símbolos "Seguros" (SYMBOL_CHARS):**
`!@#$%^&*()_+-=[]{}|;:'",.<>/?~`

Esta lista ha sido seleccionada considerando su compatibilidad a través de JavaScript (literales de cadena, expresiones regulares) y PHP (Laravel, expresiones regulares).

* **Evitado:** Caracteres que son excesivamente problemáticos para shell/CLI (`&;$><`) si se pasaran como argumentos directos sin un escape robusto (aunque en contextos web, Laravel los maneja de forma segura).
* **Manejo Especial en Regex:**
    * Caracteres como `^`, `$`, `*`, `+`, `?`, `.`, `(`, `)`, `|`, `{`, `}`, `[`, `]`, `\`, `-` tienen significados especiales en expresiones regulares.
    * Cuando se incluyen en un conjunto de caracteres `[]` dentro de una regex, algunos de ellos (como `[`, `]`, `\`, `-` si no está en inicio/fin) necesitan ser escapados (`\[`, `\]`, `\\`, `\-`).
    * La expresión regular utilizada en el código JavaScript (`new RegExp(...)`) y PHP asegura el escape correcto de todos los símbolos definidos en `SYMBOL_CHARS` para que sean interpretados literalmente.