<?php
class Role
{
    protected $permissions;

    protected function __construct() {
        $this->permissions = array();
    }

    // return a role object with associated permissions
    public static function getRolePerms($role_id) {
        
        global $db;
        
        $role = new Role();
        $query = "SELECT t2.perm_desc FROM role_perm as t1
                JOIN permissions as t2 ON t1.perm_id = t2.perm_id
                WHERE t1.role_id = :role_id";
        $statement = $db->prepare($query);
        $statement->execute(array(":role_id" => $role_id));

        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $role->permissions[$row["perm_desc"]] = true;
        }
        return $role;
    }

    // check if a permission is set
    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }

    // insert a new role
public static function insertRole($role_name) {
    
    global $db;

    $query = "INSERT INTO roles (role_name) VALUES (:role_name)";
    $statement = $db->prepare($query);
    return $statement->execute(array(":role_name" => $role_name));
}

// insert array of roles for specified user id
public static function insertUserRoles($user_id, $roles) {
    
    global $db;
    $query = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id)";
    $statement = $db->prepare($query);
    $statement->bindParam(":user_id", $user_id, PDO::PARAM_STR);
    $statement->bindParam(":role_id", $role_id, PDO::PARAM_INT);
    foreach ($roles as $role_id) {
        $statement->execute();
    }
    return true;
}

// delete array of roles, and all associations
public static function deleteRoles($roles) {
    
    global $db;
    $query = "DELETE t1, t2, t3 FROM roles as t1
            JOIN user_role as t2 on t1.role_id = t2.role_id
            JOIN role_perm as t3 on t1.role_id = t3.role_id
            WHERE t1.role_id = :role_id";
    $statement = $db->prepare($query);
    $statement->bindParam(":role_id", $role_id, PDO::PARAM_INT);
    foreach ($roles as $role_id) {
        $statement->execute();
    }
    return true;
}

// delete ALL roles for specified user id
public static function deleteUserRoles($user_id) {
    
    global $db;
    $query = "DELETE FROM user_role WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    return $statement->execute(array(":user_id" => $user_id));
}
}