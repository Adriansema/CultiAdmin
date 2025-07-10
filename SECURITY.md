### Gestion de Contrasenas Seguras

**Objetivo:** Asegurar que las contrasenas de usuario sean robustas y consistentes tanto en la validacion del frontend como del backend, y que se manejen de forma segura en las transacciones HTTP.

**Criterios de Robustez:**
Las contrasenas deben tener un minimo de 8 caracteres y contener al menos una letra minuscula, una letra mayuscula, un numero y un simbolo.

**Conjunto de Simbolos "Seguros" (SYMBOL_CHARS):**
`!@#$%^&*()_+-=[]{}|;:'",.<>/?~`

Esta lista ha sido seleccionada considerando su compatibilidad a traves de JavaScript (literales de cadena, expresiones regulares) y PHP (Laravel, expresiones regulares).

* **Evitado:** Caracteres que son excesivamente problematicos para shell/CLI (`&;$><`) si se pasaran como argumentos directos sin un escape robusto (aunque en contextos web, Laravel los maneja de forma segura).
* **Manejo Especial en Regex:**
    * Caracteres como `^`, `$`, `*`, `+`, `?`, `.`, `(`, `)`, `|`, `{`, `}`, `[`, `]`, `\`, `-` tienen significados especiales en expresiones regulares.
    * Cuando se incluyen en un conjunto de caracteres `[]` dentro de una regex, algunos de ellos (como `[`, `]`, `\`, `-` si no esta en inicio/fin) necesitan ser escapados (`\[`, `\]`, `\\`, `\-`).
    * La expresion regular utilizada en el codigo JavaScript (`new RegExp(...)`) y PHP asegura el escape correcto de todos los simbolos definidos en `SYMBOL_CHARS` para que sean interpretados literalmente.