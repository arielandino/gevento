# Gevento Products & Multi-Currency RESTful API (Laravel)

API RESTful desarrollada en **Laravel** para la gestión de productos, costos de impuestos/fabricación y registro de precios en múltiples divisas, aplicando buenas prácticas de Ingeniería de Software Backend.

---

## Arquitectura y Buenas Prácticas Implementadas

- **Arquitectura en Capas (Clean Layered Architecture)**:
  - **Controllers (`app/Http/Controllers/Api/`)**: Controladores delgados (*Thin Controllers*) encargados únicamente de orquestar la petición HTTP y retornar respuestas serializadas.
  - **Form Requests (`app/Http/Requests/`)**: Validación desacoplada, reglas estrictas, mensajes personalizados y sanitización preventiva de entradas (`strip_tags`, `trim`, normalización de campos como `Price`/`price`).
  - **Service Layer (`app/Services/`)**: Lógica de negocio encapsulada y ejecutada dentro de transacciones atómicas de base de datos (`DB::transaction`).
  - **API Resources (`app/Http/Resources/`)**: Transformación y serialización consistente de modelos Eloquent a JSON (`ProductResource`, `ProductPriceResource`, `CurrencyResource`).
  - **Eloquent ORM (`app/Models/`)**: Relaciones tipadas (`BelongsTo`, `HasMany`), *Query Scopes* para filtrado dinámico (`scopeFilter`), `SoftDeletes` para borrado lógico seguro y *Attribute Casting* (`decimal:2`, `decimal:4`).
- **Seguridad**:
  - **Rate Limiting**: Limitador de peticiones configurado a 120 req/min por IP (`RateLimiter::for('api')`).
  - **Security Headers (`SecurityHeadersMiddleware`)**: Cabeceras `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `X-XSS-Protection`, `Referrer-Policy` y `Permissions-Policy`.
  - **Respuestas JSON Forzadas (`ForceJsonResponseMiddleware`)**: Garantiza que cualquier error (`404`, `422`, `500`) devuelva siempre una estructura JSON uniforme.
  - **Protección de Directorios (`.htaccess`)**: Bloqueo de acceso directo a carpetas internas cuando se ejecuta en entornos compartidos o Devilbox `public_html`.

---

## Cómo levantarlo en otra PC

```bash
docker compose up -d --build
```

Una vez iniciado:

- **API**: http://localhost:8080/products
- **Swagger UI**: http://localhost:8080/swagger/index.html
- **Correr los 36 tests en el contenedor**:

```bash
docker compose exec app php artisan test
```

---

## Ejecución en Devilbox (`http://gevento.loc`)

El proyecto ya se encuentra configurado y corriendo sobre tu instancia actual de **Devilbox**:

- **URL Base (Devilbox)**: `http://gevento.loc` (también disponible bajo prefijo `http://gevento.loc/api`)
- **Swagger UI Interactivo**: [http://gevento.loc/swagger/index.html](http://gevento.loc/swagger/index.html) (o `http://gevento.loc/api/documentation`)
- **Base de Datos MySQL/MariaDB**: `gevento` (con detección inteligente Docker/Host en `config/database.php`: usa `mysql:3306` dentro del contenedor Devilbox y `127.0.0.1:3400` desde la terminal de macOS).

### Comandos útiles

```bash
# Ejecutar migraciones y seeders (datos de prueba: USD, EUR, ARS, MXN, COP + productos)
php artisan migrate:fresh --seed

# Ejecutar la suite de pruebas automatizadas (PHPUnit)
php artisan test
```

---

## Endpoints Disponibles

| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| `GET` | `/products` | Obtener lista de productos (soporta `?search=`, `?currency_id=`, `?min_price=`, `?max_price=`, `?per_page=`) |
| `POST` | `/products` | Crear un nuevo producto |
| `GET` | `/products/{id}` | Obtener un producto por ID |
| `PUT` | `/products/{id}` | Actualizar un producto existente |
| `DELETE` | `/products/{id}` | Eliminar un producto (Soft Delete) |
| `GET` | `/products/{id}/prices` | Obtener lista de precios registrados de un producto |
| `POST` | `/products/{id}/prices` | Crear o actualizar un precio para un producto en una divisa |
| `GET` | `/currencies` | Obtener catálogo de divisas |
| `POST` | `/currencies` | Crear una nueva divisa |

---

## Documentación de la API (`swagger/`)

Dentro de la carpeta [`swagger/`](./swagger/) encontrarás:

1. [`swagger/index.html`](./swagger/index.html): Interfaz interactiva **Swagger UI** lista para probar todos los endpoints desde el navegador.
2. [`swagger/openapi.json`](./swagger/openapi.json): Especificación **OpenAPI 3.0.3** en formato JSON.
3. [`swagger/openapi.yaml`](./swagger/openapi.yaml): Especificación **OpenAPI 3.0.3** en formato YAML.
4. [`swagger/postman_collection.json`](./swagger/postman_collection.json): Colección lista para importar en **Postman** o **Insomnia**.
