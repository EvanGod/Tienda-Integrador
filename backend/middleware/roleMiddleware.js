// middleware/roleMiddleware.js
const checkRole = (roles) => {
    return async (req, res, next) => {
      try {
        const userRole = req.user.role; // Aquí deberías tener el rol del usuario
  
        if (!roles.includes(userRole)) {
          return res.status(403).json({ message: 'Acceso denegado: Rol no autorizado' });
        }
  
        next();
      } catch (error) {
        console.error('Error en checkRole:', error);
        res.status(500).json({ message: 'Error del servidor al verificar el rol' });
      }
    };
  };
  
  module.exports = checkRole;
  