const productoModel = require('../models/productoModel');

// Crear un producto
const crearProducto = async (req, res) => {
  try {
    const { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado } = req.body;
    if (!idcategoria || !nombre || !precio_venta || !stock) {
      return res.status(400).json({ message: 'Faltan datos obligatorios' });
    }

    const producto = { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado: estado || 1 };
    const result = await productoModel.crearProducto(producto);
    res.status(201).json({ message: 'Producto creado', productoId: result.insertId });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al crear producto' });
  }
};

// Obtener todos los productos
const obtenerProductos = async (req, res) => {
  try {
    const productos = await productoModel.obtenerProductos();
    res.status(200).json(productos);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al obtener productos' });
  }
};

// Obtener un producto por ID
const obtenerProductoPorId = async (req, res) => {
  try {
    const { idarticulo } = req.params;
    const producto = await productoModel.obtenerProductoPorId(idarticulo);
    if (producto.length === 0) {
      return res.status(404).json({ message: 'Producto no encontrado' });
    }
    res.status(200).json(producto[0]);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al obtener producto' });
  }
};

// Actualizar un producto
const actualizarProducto = async (req, res) => {
  try {
    const { idarticulo } = req.params;
    const { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado } = req.body;
    const producto = { idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado };

    const result = await productoModel.actualizarProducto(idarticulo, producto);
    if (result.affectedRows === 0) {
      return res.status(404).json({ message: 'Producto no encontrado' });
    }
    res.status(200).json({ message: 'Producto actualizado' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al actualizar producto' });
  }
};

// Eliminar un producto
const eliminarProducto = async (req, res) => {
  try {
    const { idarticulo } = req.params;
    const result = await productoModel.eliminarProducto(idarticulo);
    if (result.affectedRows === 0) {
      return res.status(404).json({ message: 'Producto no encontrado' });
    }
    res.status(200).json({ message: 'Producto eliminado' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Error al eliminar producto' });
  }
};

module.exports = { crearProducto, obtenerProductos, obtenerProductoPorId, actualizarProducto, eliminarProducto };
