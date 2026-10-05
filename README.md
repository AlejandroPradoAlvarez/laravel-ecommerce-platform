# NovaStore · Ecommerce con Laravel

NovaStore es una aplicación ecommerce de demostración desarrollada con Laravel 10. El proyecto está orientado a portfolio y muestra un flujo completo de tienda con catálogo, carrito, perfil de cliente, pedidos y un panel de administración protegido por roles.

## Funcionalidades

### Cliente

- Registro e inicio de sesión con Laravel UI.
- Catálogo público con búsqueda, filtro por categoría y ordenación.
- Ficha de producto y control de stock.
- Carrito persistido en sesión con actualización de cantidades.
- Perfil editable con datos de contacto y envío.
- Checkout simulado y creación de pedidos.
- Historial y detalle de pedidos.
- Autorización para impedir el acceso a pedidos de otros usuarios.

### Administración

- Dashboard con métricas de productos, clientes, pedidos y facturación demo.
- CRUD de productos con precio, stock, categoría y visibilidad.
- Gestión de categorías.
- Listado de pedidos con filtro por estado.
- Cambio de estado del pedido: pendiente, pagado, preparando, enviado, entregado o cancelado.
- Middleware de administrador para proteger todo `/admin`.

## Decisiones técnicas

- Laravel 10 y PHP 8.1+.
- MySQL en desarrollo y SQLite en memoria para tests.
- Eloquent y relaciones entre usuarios, pedidos, líneas de pedido, productos y categorías.
- Form Requests para la validación de productos y perfil.
- Policy para la autorización de pedidos.
- Transacción de base de datos y bloqueo pesimista al confirmar un pedido para revalidar stock.
- Bootstrap 5 y Vite para la interfaz.
- Seeders con catálogo y cuentas demo.
- Tests funcionales para catálogo, carrito, checkout, autorización, perfil y administración.

## Modelo de datos

```text
User 1 ─── N Order 1 ─── N OrderItem N ─── 1 Product N ─── 1 Category
```

## Instalación

```bash
git clone <url-del-repositorio>
cd tienda-laravel
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env` y después ejecuta:

```bash
php artisan migrate --seed
npm run dev
php artisan serve
```

## Usuarios de demostración

Después de ejecutar `php artisan migrate --seed`:

| Rol | Email | Contraseña |
| --- | --- | --- |
| Administrador | `admin@example.com` | `password` |
| Cliente | `cliente@example.com` | `password` |

Estas credenciales son exclusivamente datos de demostración generados por el seeder.

## Tests

```bash
php artisan test
```

Los tests utilizan SQLite en memoria, por lo que PHP debe tener habilitada la extensión `pdo_sqlite`.

## Preparación para GitHub

El repositorio no debe incluir `.env`, `vendor/`, `node_modules/`, logs ni cachés generadas. `.env.example`, `composer.lock` y `package-lock.json` sí deben mantenerse versionados para facilitar una instalación reproducible.

## Ideas de evolución

- Pasarela de pago en entorno sandbox.
- Subida real de imágenes mediante Storage.
- Cupones y promociones.
- Gestión de devoluciones.
- API REST para un cliente móvil o SPA.
