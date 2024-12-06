exports.insertarDetalleVenta = `
    INSERT INTO detalle_venta (idventa, idarticulo, cantidad, precio, descuento) 
    VALUES (?, ?, ?, ?, ?)
`;


// queriesDetalleVenta.js
exports.obtenerDetallesPorIdVenta = `
  SELECT d.iddetalle_venta, a.nombre, d.cantidad, d.precio, d.descuento
  FROM detalle_venta d
  JOIN articulo a ON d.idarticulo = a.idarticulo
  WHERE d.idventa = ?;
`;