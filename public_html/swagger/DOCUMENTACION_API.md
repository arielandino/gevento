# Documentación Técnica de la API — Gestión de Productos y Precios Multi-Divisa

## 1. Información General

- **Base URL (Devilbox)**: `http://gevento.loc` (también disponible bajo `http://gevento.loc/api`)
- **Formato de Intercambio**: `application/json`
- **ORM**: Laravel Eloquent
- **Cabeceras recomendadas**:
  - `Accept: application/json`
  - `Content-Type: application/json`

---

## 2. Modelo de Datos

### 2.1 Tabla `currencies` (Divisas)
| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | `integer` | Identificador único de la divisa |
| `name` | `string` | Nombre/código único de la divisa (ej. `USD`, `EUR`, `ARS`) |
| `symbol` | `string` | Símbolo de la divisa (ej. `$`, `€`) |
| `exchange_rate` | `decimal(15,4)` | Tasa de cambio respecto a la divisa base |

### 2.2 Tabla `products` (Productos)
| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | `integer` | Identificador único del producto |
| `name` | `string` | Nombre del producto |
| `description` | `text` | Descripción del producto |
| `price` | `decimal(15,2)` | Precio del producto en la divisa base |
| `currency_id` | `integer` | Identificador de la divisa base (FK -> `currencies.id`) |
| `tax_cost` | `decimal(15,2)` | Costo de impuestos del producto |
| `manufacturing_cost` | `decimal(15,2)` | Costo de fabricación del producto |

### 2.3 Tabla `product_prices` (Precios de productos)
| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | `integer` | Identificador único del registro de precio |
| `product_id` | `integer` | Identificador del producto (FK -> `products.id`) |
| `currency_id` | `integer` | Identificador de la divisa (FK -> `currencies.id`) |
| `price` | `decimal(15,2)` | Precio del producto en la divisa especificada |

---

## 3. Especificación de Endpoints

### 3.1 `GET /products` — Obtener lista de productos

Obtiene la colección de productos registrados con su divisa base y precios adicionales.

**Parámetros Query Opcionales:**
- `search` (string): Filtra por coincidencia en nombre o descripción.
- `currency_id` (int): Filtra por ID de divisa base.
- `min_price` / `max_price` (decimal): Filtra por rango de precio.
- `per_page` (int, 1-100): Activa paginación con la cantidad indicada.

**Respuesta Exitosa (`200 OK`):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "MacBook Pro 16\" M3 Max",
      "description": "Portátil profesional de alto rendimiento con chip M3 Max, 36GB RAM y 1TB SSD.",
      "price": 3499,
      "currency_id": 1,
      "tax_cost": 349.9,
      "manufacturing_cost": 1850,
      "currency": {
        "id": 1,
        "name": "USD",
        "symbol": "$",
        "exchange_rate": 1
      },
      "prices": [
        {
          "id": 1,
          "product_id": 1,
          "currency_id": 2,
          "price": 3219.08,
          "currency": {
            "id": 2,
            "name": "EUR",
            "symbol": "€",
            "exchange_rate": 0.92
          }
        }
      ]
    }
  ],
  "success": true,
  "message": "Lista de productos obtenida correctamente."
}
```

---

### 3.2 `POST /products` — Crear un nuevo producto

**Body (`application/json`):**
```json
{
  "name": "Auriculares Sony WH-1000XM5",
  "description": "Auriculares inalámbricos con cancelación de ruido activa.",
  "price": 399.99,
  "currency_id": 1,
  "tax_cost": 40.00,
  "manufacturing_cost": 180.00
}
```

**Respuesta Exitosa (`201 Created`):**
```json
{
  "data": {
    "id": 4,
    "name": "Auriculares Sony WH-1000XM5",
    "description": "Auriculares inalámbricos con cancelación de ruido activa.",
    "price": 399.99,
    "currency_id": 1,
    "tax_cost": 40,
    "manufacturing_cost": 180,
    "currency": {
      "id": 1,
      "name": "USD",
      "symbol": "$",
      "exchange_rate": 1
    },
    "prices": []
  },
  "success": true,
  "message": "Producto creado correctamente."
}
```

---

### 3.3 `GET /products/{id}` — Obtener un producto por ID

**Respuesta Exitosa (`200 OK`):** Retorna el objeto del producto en `data`.
**Respuesta Error (`404 Not Found`):**
```json
{
  "success": false,
  "message": "El recurso solicitado no fue encontrado.",
  "data": null
}
```

---

### 3.4 `PUT /products/{id}` — Actualizar un producto

**Body (`application/json`):**
```json
{
  "name": "Auriculares Sony WH-1000XM5 (Edición Especial)",
  "price": 419.99,
  "tax_cost": 42.00
}
```

**Respuesta Exitosa (`200 OK`):** Retorna el producto actualizado en `data`.

---

### 3.5 `DELETE /products/{id}` — Eliminar un producto

Realiza el borrado lógico (`SoftDelete`) del producto.

**Respuesta Exitosa (`200 OK`):**
```json
{
  "success": true,
  "message": "Producto eliminado correctamente.",
  "data": null
}
```

---

### 3.6 `GET /products/{id}/prices` — Obtener lista de precios de un producto

Retorna todos los precios registrados en distintas divisas para el producto `{id}`.

**Respuesta Exitosa (`200 OK`):**
```json
{
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "currency_id": 2,
      "price": 3219.08,
      "currency": {
        "id": 2,
        "name": "EUR",
        "symbol": "€",
        "exchange_rate": 0.92
      }
    }
  ],
  "success": true,
  "message": "Lista de precios del producto obtenida correctamente."
}
```

---

### 3.7 `POST /products/{id}/prices` — Crear un nuevo precio para un producto

Registra o actualiza el precio del producto `{id}` en la divisa indicada.

**Body (`application/json`):**
```json
{
  "currency_id": 2,
  "price": 3219.08
}
```

**Respuesta Exitosa (`201 Created`):**
```json
{
  "data": {
    "id": 1,
    "product_id": 1,
    "currency_id": 2,
    "price": 3219.08,
    "currency": {
      "id": 2,
      "name": "EUR",
      "symbol": "€",
      "exchange_rate": 0.92
    }
  },
  "success": true,
  "message": "Precio del producto registrado correctamente."
}
```
