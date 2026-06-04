<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereUpdatedAt($value)
 */
	class Categoria extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property numeric $total
 * @property int $proveedor_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DetalleCompra> $detalles
 * @property-read int|null $detalles_count
 * @property-read \App\Models\Proveedor|null $proveedor
 * @property-read \App\Models\User|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra whereProveedorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Compra whereUserId($value)
 */
	class Compra extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cantidad
 * @property numeric $precio_compra
 * @property numeric $subtotal
 * @property int $compra_id
 * @property int $producto_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Compra|null $compra
 * @property-read \App\Models\Producto|null $producto
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereCantidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereCompraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra wherePrecioCompra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereProductoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleCompra whereUpdatedAt($value)
 */
	class DetalleCompra extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cantidad
 * @property numeric $precio_unitario
 * @property numeric $subtotal
 * @property int $venta_id
 * @property int $producto_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Producto|null $producto
 * @property-read \App\Models\Venta|null $venta
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereCantidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta wherePrecioUnitario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereProductoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetalleVenta whereVentaId($value)
 */
	class DetalleVenta extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Empleado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Empleado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Empleado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Empleado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Empleado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Empleado whereUpdatedAt($value)
 */
	class Empleado extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lote whereUpdatedAt($value)
 */
	class Lote extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $codigo_barras
 * @property string $nombre
 * @property numeric $precio_compra
 * @property numeric $precio_venta
 * @property int $stock
 * @property int $stock_minimo
 * @property string|null $imagen
 * @property int $sub_categoria_id
 * @property int $proveedor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Proveedor|null $proveedor
 * @property-read \App\Models\SubCategoria|null $subcategoria
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereCodigoBarras($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereImagen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto wherePrecioCompra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto wherePrecioVenta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereProveedorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereStockMinimo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereSubCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereUpdatedAt($value)
 */
	class Producto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Compra> $compras
 * @property-read int|null $compras_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proveedor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proveedor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proveedor query()
 */
	class Proveedor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereUpdatedAt($value)
 */
	class Rol extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property int $categoria_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Categoria|null $categoria
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria whereCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategoria whereUpdatedAt($value)
 */
	class SubCategoria extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $cliente
 * @property numeric $total
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DetalleVenta> $detalles
 * @property-read int|null $detalles_count
 * @property-read \App\Models\User|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta whereCliente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venta whereUserId($value)
 */
	class Venta extends \Eloquent {}
}

