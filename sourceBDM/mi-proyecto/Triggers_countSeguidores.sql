DELIMITER $$

CREATE TRIGGER after_insert_seguidor
AFTER INSERT ON Seguidores
FOR EACH ROW
BEGIN
    UPDATE Usuarios
    SET N_seguidores = N_seguidores + 1
    WHERE ID = NEW.SeguidoID;
END$$

CREATE TRIGGER after_delete_seguidor
AFTER DELETE ON Seguidores
FOR EACH ROW
BEGIN
    UPDATE Usuarios
    SET N_seguidores = N_seguidores - 1
    WHERE ID = OLD.SeguidoID;
END$$

DELIMITER ;
