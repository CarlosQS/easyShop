# API REST en Laravel
Este es un proyecto de API REST en Laravel que proporciona endpoints para crear, editar, eliminar y obtener información de las tiendas y sus productos.

## Requisitos
- PHP >= 7.2.5
- Laravel >= 7.x
- Composer

## Configuración
1. Clona el proyecto desde el repositorio remoto
2. Ejecuta el siguiente comando para instalar las dependencias:
```bash
composer install
```
3. Crea un archivo `.env` y copia el contenido del archivo `.env.example` a `.env`
4. Configura las variables de entorno en el archivo .env
5. Ejecuta el siguiente comando para generar una clave de cifrado:
```vbnet
php artisan key:generate
```
6. Ejecuta el siguiente comando para migrar las tablas de la base de datos:
```bash
php artisan migrate
```

## Endpoints
### Obtener todas las tiendas y sus productos
```bash
GET /api/tiendas
```
Devuelve un JSON con todas las tiendas y sus productos en formato legible.

### Obtener una tienda y sus productos por ID
```bash
GET /api/tiendas/{id}
```
Devuelve un JSON con la tienda y sus productos correspondientes al ID proporcionado en formato legible.

### Obtener todos los productos
```bash
GET /api/productos
```
Devuelve un JSON con todos los productos en formato legible.

### Crear una tienda con sus productos
```bash
POST /api/tiendas
```
Crea una nueva tienda con el nombre proporcionado en el cuerpo de la solicitud y sus productos proporcionados en formato JSON. Devuelve un JSON con la tienda y sus productos en formato legible.

Ejemplo de solicitud:

```bash
POST /api/tiendas
Content-Type: application/json

{
    "nombre": "NewShop",
    "productos": [
        {"id":1,"cantidad":4},
        {"id":2,"cantidad":2},
        {"id":3,"cantidad":5}
    ]
}
```

### Editar una tienda por ID
```bash
PUT /api/tiendas/{id}
```
Edita la tienda correspondiente al ID proporcionado en el cuerpo de la solicitud y devuelve un JSON con la tienda editada en formato legible.

Ejemplo de solicitud:

```bash
PUT /api/tiendas/15
Content-Type: application/json

{
    "nombre": "editedShop"
}
```

### Eliminar una tienda por ID
```bash
DELETE /api/tiendas/{id}
```
Elimina la tienda correspondiente al ID proporcionado y devuelve un mensaje de éxito en formato JSON.

Ejemplo de solicitud:

```bash
DELETE /api/tiendas/15
```