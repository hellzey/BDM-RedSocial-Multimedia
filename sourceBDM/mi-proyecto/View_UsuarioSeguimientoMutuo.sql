CREATE VIEW UsuariosSeguimientoMutuo AS
SELECT u.ID, u.Nick
FROM Usuarios u
INNER JOIN Seguidores s1 ON s1.SeguidoID = u.ID
INNER JOIN Seguidores s2 ON s2.SeguidorID = u.ID AND s2.SeguidoID = s1.SeguidorID;
