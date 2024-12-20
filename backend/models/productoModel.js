const pool = require('../db/connection');

// Crear un nuevo producto
const crearProducto = async (producto) => {
  const { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado } = producto;
  const query = `INSERT INTO articulo (idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado) 
                 VALUES (?, ?, ?, 0, 0, ?, 0)`;
  const [result] = await pool.execute(query, [idcategoria, codigo, nombre, descripcion]);
  return result;
};

const obtenerProductos = async () => {
  const query = `
    SELECT 
      a.idarticulo, 
      a.nombre, 
      a.codigo, 
      a.precio_venta, 
      a.stock, 
      a.descripcion, 
      a.estado, 
      c.nombre AS categoria 
    FROM 
      articulo a
    JOIN 
      categoria c ON a.idcategoria = c.idcategoria
    WHERE
      a.precio_venta != 0;
  `;
  const [result] = await pool.execute(query);
  return result;
};

const obtenerProductosVenta = async () => {
  const query = `
    SELECT 
      a.idarticulo, 
      a.nombre, 
      a.codigo, 
      a.precio_venta, 
      a.stock, 
      a.descripcion, 
      a.estado, 
      c.nombre AS categoria 
    FROM 
      articulo a
    JOIN 
      categoria c ON a.idcategoria = c.idcategoria
    WHERE 
      a.estado = 1
  `;
  const [result] = await pool.execute(query);
  return result;
};


// Obtener un producto por ID con el nombre de la categoría
const obtenerProductoPorId = async (idarticulo) => {
  const query = `
    SELECT 
      articulo.idarticulo, 
      articulo.nombre AS articulo_nombre, 
      articulo.codigo,
      articulo.descripcion, 
      articulo.precio_venta, 
      articulo.stock,
      articulo.estado, 
      categoria.nombre AS categoria_nombre
    FROM 
      articulo
    INNER JOIN 
      categoria 
    ON 
      articulo.idcategoria = categoria.idcategoria
    WHERE 
      articulo.idarticulo = ?`;
    
  const [result] = await pool.execute(query, [idarticulo]);
  return result;
};


// Actualizar un producto (solo categoría, código, descripción y estado)
const actualizarProducto = async (idarticulo, producto) => {
  const { codigo, descripcion, estado } = producto;
  const query = `UPDATE articulo SET  codigo = ?, descripcion = ?, estado = ? 
                 WHERE idarticulo = ?`;
  const [result] = await pool.execute(query, [codigo, descripcion, estado, idarticulo]);
  return result;
};

// Eliminar un producto (cambiar estado)
const eliminarProducto = async (idarticulo) => {
  const query = `UPDATE articulo SET estado = 0 WHERE idarticulo = ?`;
  const [result] = await pool.execute(query, [idarticulo]);
  return result;
};

module.exports = { crearProducto, obtenerProductos, obtenerProductoPorId, actualizarProducto, eliminarProducto, obtenerProductosVenta };
