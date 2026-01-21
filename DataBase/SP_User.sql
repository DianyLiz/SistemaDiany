DELIMITER //
CREATE PROCEDURE SP_User (
	IN _id INT,
	IN _email VARCHAR(100),
	IN _password VARCHAR(255),
	IN _descripcion TEXT,
	IN _estado TINYINT,
	IN _creacion TIMESTAMP
	
) 
BEGIN

	DECLARE _Exists INTEGER;
	SELECT COUNT(id) FROM Users WHERE id = _id INTO _Exists;
	
	IF _Exists > 0 THEN
		UPDATE Users SET `email` = _email, `password` = _password, descripcion = _descripcion, estado = _estado, creacion = _creacion WHERE id = _id;
		
	ELSE
		INSERT INTO Users (id, email, `password`, descripcion, estado, creacion) VALUES (_id, _email, _password, _descripcion, _estado, _creacion);	 
	END IF;
END // 