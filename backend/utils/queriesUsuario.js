exports.obtenerIdUsuario = `
    SELECT idusuario 
    FROM usuario 
    WHERE email = ?
`;
