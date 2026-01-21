CREATE TABLE notifications (  
    id INT PRIMARY KEY IDENTITY(1,1),  
    user_id INT NOT NULL,  
    message NVARCHAR(255) NOT NULL,  
    notification_type NVARCHAR(50) NOT NULL,  
    read_status BIT DEFAULT 0,  
    created_at DATETIME DEFAULT GETDATE(),  
    updated_at DATETIME DEFAULT GETDATE(),  
    FOREIGN KEY (user_id) REFERENCES users(id)  
);