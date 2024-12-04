exports.insertarDetalleVenta = `
    INSERT INTO detalle_venta (idventa, idarticulo, cantidad, precio, descuento) 
    VALUES (?, ?, ?, ?, ?)
`;
