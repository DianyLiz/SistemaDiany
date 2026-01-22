SELECT u.id, u.email, u.password, u.descripcion, u.estado, u.creacion
                            FROM users u
                            ORDER BY u.id DESC
                            
                            
                            ALTER TABLE Users
MODIFY PASSWORD VARCHAR(255) NOT NULL;
