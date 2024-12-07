use integrador;

create table categoria(
       idcategoria integer primary key auto_increment,
       nombre varchar(50) not null unique,
       descripcion varchar(256) null,
       estado bit default(1)
);

create table articulo(
       idarticulo integer primary key auto_increment,
       idcategoria integer not null,
       codigo varchar(50) null,
       nombre varchar(100) not null unique,
       precio_venta decimal(11,2) not null,
       stock integer not null,
       descripcion varchar(256) null,
       estado bit default(1),
       FOREIGN KEY (idcategoria) REFERENCES categoria(idcategoria)
);

create table persona(
       idpersona integer primary key auto_increment,
       tipo_persona varchar(20) not null,
       nombre varchar(100) not null,
       tipo_documento varchar(20) null,
       num_documento varchar(20) null,
       direccion varchar(70) null,
       telefono varchar(20) null,
       email varchar(50) null
);

create table rol(
       idrol integer primary key auto_increment,
       nombre varchar(30) not null,
       descripcion varchar(100) null,
       estado bit default(1)
);

create table usuario(
       idusuario integer primary key auto_increment,
       idrol integer not null,
       nombre varchar(100) not null,
       tipo_documento varchar(20) null,
       num_documento varchar(20) null,
       direccion varchar(70) null,
       telefono varchar(20) null,
       email varchar(50) not null,
       password VARCHAR(256) not null,
       estado bit default(1),
       FOREIGN KEY (idrol) REFERENCES rol (idrol)
);

create table ingreso(
       idingreso integer primary key auto_increment,
       idproveedor integer not null,
       idusuario integer not null,
       tipo_comprobante varchar(20) not null,
       serie_comprobante varchar(7) null,
       num_comprobante varchar (10) not null,
       fecha datetime not null,
       impuesto decimal (4,2) not null,
       total decimal (11,2) not null,
       estado varchar(20) not null,
       FOREIGN KEY (idproveedor) REFERENCES persona (idpersona),
       FOREIGN KEY (idusuario) REFERENCES usuario (idusuario)
);

create table detalle_ingreso(
       iddetalle_ingreso integer primary key auto_increment,
       idingreso integer not null,
       idarticulo integer not null,
       cantidad integer not null,
       precio decimal(11,2) not null,
       FOREIGN KEY (idingreso) REFERENCES ingreso (idingreso) ON DELETE CASCADE,
       FOREIGN KEY (idarticulo) REFERENCES articulo (idarticulo)
);


create table venta(
       idventa integer primary key auto_increment,
       idcliente integer not null,
       idusuario integer not null,
       tipo_comprobante varchar(20) not null,
       serie_comprobante varchar(7) null,
       num_comprobante varchar (10) not null,
       fecha_hora datetime not null,
       impuesto decimal (4,2) not null,
       total decimal (11,2) not null,
       estado varchar(20) not null,
       FOREIGN KEY (idcliente) REFERENCES persona (idpersona),
       FOREIGN KEY (idusuario) REFERENCES usuario (idusuario)
);

create table detalle_venta(
       iddetalle_venta integer primary key auto_increment,
       idventa integer not null,
       idarticulo integer not null,
       cantidad integer not null,
       precio decimal(11,2) not null,
       descuento decimal(11,2) not null,
       FOREIGN KEY (idventa) REFERENCES venta (idventa) ON DELETE CASCADE,
       FOREIGN KEY (idarticulo) REFERENCES articulo (idarticulo)
);








INSERT INTO categoria (nombre, descripcion, estado) VALUES
('Electrónica', 'Productos electrónicos como teléfonos, computadoras, etc.', 1),
('Ropa', 'Ropa de diferentes estilos y tamaños', 1),
('Hogar', 'Artículos para el hogar como muebles, utensilios, etc.', 1),
('Deportes', 'Artículos deportivos y equipos', 1),
('Juguetes', 'Juguetes para niños y adultos', 1),
('Alimentos', 'Productos alimenticios y bebidas', 1),
('Libros', 'Libros de diferentes géneros', 1),
('Jardinería', 'Herramientas y productos para el jardín', 1),
('Belleza', 'Productos de belleza y cuidado personal', 1),
('Automotriz', 'Accesorios y productos para vehículos', 1);


INSERT INTO articulo (idcategoria, codigo, nombre, precio_venta, stock, descripcion, estado) VALUES
(1, 'EL001', 'Smartphone Samsung Galaxy', 300.00, 50, 'Smartphone con pantalla de 6.5 pulgadas, 128GB de almacenamiento.', 1),
(1, 'EL002', 'Laptop Dell XPS 13', 1000.00, 30, 'Laptop de alta gama con procesador Intel Core i7, 16GB RAM.', 1),
(2, 'RP001', 'Camisa Polo', 25.00, 100, 'Camisa de algodón con diseño clásico', 1),
(2, 'RP002', 'Pantalón Levi\'s 501', 50.00, 80, 'Pantalón de mezclilla, corte recto', 1),
(3, 'HG001', 'Sofá de 3 plazas', 500.00, 20, 'Sofá cómodo con tapizado de tela', 1),
(3, 'HG002', 'Lámpara de pie LED', 60.00, 40, 'Lámpara de pie con luz LED regulable', 1),
(4, 'DP001', 'Balón de fútbol Adidas', 20.00, 150, 'Balón de fútbol profesional de alta calidad', 1),
(4, 'DP002', 'Raqueta de tenis Wilson', 70.00, 60, 'Raqueta profesional de tenis con cuerda ajustable', 1),
(5, 'JY001', 'Muñeca Barbie', 15.00, 200, 'Muñeca Barbie con ropa y accesorios', 1),
(5, 'JY002', 'Tren eléctrico', 100.00, 50, 'Tren eléctrico con control remoto', 1);

INSERT INTO persona (tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email) VALUES
('Proveedor', 'Juan Pérez', 'INE', '12345678', 'Av. Libertad 123', '987654321', 'juanperez@gmail.com'),
('Cliente', 'María López', 'INE', '87654321', 'Calle Ficticia 456', '987123654', 'marialopez@hotmail.com'),
('Proveedor', 'Carlos Gómez', 'RUC', '1234567890', 'Av. Pardo 789', '987987987', 'carlosgomez@proveedor.com'),
('Cliente', 'Ana Ruiz', 'INE', '23456789', 'Calle Real 101', '987654123', 'anaruiz@correo.com'),
('Proveedor', 'Laura Fernández', 'INE', '34567890', 'Av. Brasil 321', '987321456', 'laurafernandez@proveedor.com'),
('Cliente', 'José Martínez', 'INE', '45678901', 'Calle Mar 202', '987987654', 'josemartinez@cliente.com'),
('Proveedor', 'Pedro Gómez', 'RUC', '9876543210', 'Calle Central 303', '987456123', 'pedrogomez@proveedor.com'),
('Cliente', 'Lucía González', 'INE', '56789012', 'Av. Perú 404', '987123789', 'luciagonzalez@cliente.com'),
('Proveedor', 'Luis Vargas', 'RUC', '2345678901', 'Calle del Sol 505', '987654987', 'luisvargas@proveedor.com'),
('Cliente', 'Elena Soto', 'INE', '67890123', 'Av. Siempre Viva 606', '987321789', 'elenasoto@cliente.com');


INSERT INTO rol (nombre, descripcion, estado) VALUES
('Administrador', 'Rol de administrador con acceso total a todo el sistema', 1),
('Encargado', 'Rol para encargados, pueden registrar ingresos y manejar productos', 1),
('Empleado', 'Rol para empleados, pueden registrar ventas', 1);


INSERT INTO usuario (idrol, nombre, tipo_documento, num_documento, direccion, telefono, email, password, estado) VALUES
(1, 'Administrador 1', 'INE', '11111111', 'Av. Admin 1', '987654321', 'admin1@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(2, 'Encargado 1', 'INE', '22222222', 'Calle Encargado 1', '987654322', 'encargado1@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(3, 'Empleado 1', 'INE', '33333333', 'Calle Empleado 1', '987654323', 'empleado1@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(1, 'Administrador 2', 'INE', '44444444', 'Av. Admin 2', '987654324', 'admin2@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(2, 'Encargado 2', 'INE', '55555555', 'Calle Encargado 2', '987654325', 'encargado2@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(3, 'Empleado 2', 'INE', '66666666', 'Calle Empleado 2', '987654326', 'empleado2@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(1, 'Administrador 3', 'INE', '77777777', 'Av. Admin 3', '987654327', 'admin3@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(2, 'Encargado 3', 'INE', '88888888', 'Calle Encargado 3', '987654328', 'encargado3@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1),
(3, 'Empleado 3', 'INE', '99999999', 'Calle Empleado 3', '987654329', 'empleado3@tienda.com', '$2y$12$3LJQN8tk/nRYaok6OuV.u.0.xP8s0amkJx87vywgCSFzd.diyUNhu', 1);


INSERT INTO ingreso (idproveedor, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha, impuesto, total, estado) VALUES
(1, 1, 'Factura', 'F001', '00001', '2024-10-01', 18.00, 500.00, 'Activo'),
(2, 2, 'Factura', 'F002', '00002', '2024-10-02', 18.00, 700.00, 'Activo'),
(3, 3, 'Factura', 'F003', '00003', '2024-10-03', 18.00, 1000.00, 'Activo'),
(4, 4, 'Factura', 'F004', '00004', '2024-10-04', 18.00, 1200.00, 'Activo'),
(5, 5, 'Factura', 'F005', '00005', '2024-10-05', 18.00, 800.00, 'Activo');


INSERT INTO detalle_ingreso (idingreso, idarticulo, cantidad, precio) VALUES
(1, 1, 20, 300.00),
(2, 2, 15, 1000.00),
(3, 3, 10, 500.00),
(4, 4, 25, 60.00),
(5, 5, 18, 50.00);

INSERT INTO venta (idcliente, idusuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha_hora, impuesto, total, estado) VALUES
(1, 3, 'Boleta', 'B001', '00001', '2024-10-01 10:00:00', 18.00, 100.00, 'Activo'),
(2, 4, 'Boleta', 'B002', '00002', '2024-10-02 11:30:00', 18.00, 150.00, 'Activo'),
(3, 5, 'Boleta', 'B003', '00003', '2024-10-03 14:00:00', 18.00, 200.00, 'Activo'),
(4, 6, 'Boleta', 'B004', '00004', '2024-10-04 16:00:00', 18.00, 250.00, 'Activo'),
(5, 7, 'Boleta', 'B005', '00005', '2024-10-05 18:00:00', 18.00, 300.00, 'Activo');


INSERT INTO detalle_venta (idventa, idarticulo, cantidad, precio, descuento) VALUES
(1, 1, 1, 100.00, 0.00),
(2, 2, 2, 75.00, 0.00),
(3, 3, 3, 67.00, 0.00),
(4, 4, 4, 60.00, 0.00),
(5, 5, 5, 50.00, 0.00);
