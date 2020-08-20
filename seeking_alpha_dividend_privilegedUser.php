<?php
class PrivilegedUser 
{
    private $roles;

    public function __construct() {
        //parent::__construct();
    }

    // override User method
    public static function getByUsername($username) {
        
        global $db;
        
        $sql = "SELECT * FROM users WHERE user_name = :username";
        $sth = $db->prepare($sql);
        $sth->execute(array(":username" => $username));
        $result = $sth->fetchAll();

        if (!empty($result)) {
            $privUser = new PrivilegedUser();
            $privUser->user_id = $result[0]["user_id"];
            $privUser->user_name = $username;
            $privUser->password = $result[0]["password"];
            //$privUser->email_addr = $result[0]["email_addr"];
            $privUser->initRoles();
            return $privUser;
        } else {
            return false;
        }
    }

    // populate roles with their associated permissions
    protected function initRoles() {

        global $db;

        $this->roles = array();
        $sql = "SELECT t1.role_id, t2.role_name FROM user_role as t1
                JOIN roles as t2 ON t1.role_id = t2.role_id
                WHERE t1.user_id = :user_id";
        $sth = $db->prepare($sql);
        $sth->execute(array(":user_id" => $this->user_id));

        while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
        }
    }

    // check if user has a specific privilege
    public function hasPrivilege($perm) {
        foreach ($this->roles as $role) {
            if ($role->hasPerm($perm)) {
                return true;
            }
        }        
        echo "<br/><br/><div class = 'row d-flex justify-content-center'><pre> Permission Denied </pre></div><br/>";
        return false;
    }

    // check if a user has a specific role
    public function hasRole($role_name) {
        return isset($this->roles[$role_name]);
    }

    // insert a new role permission association
    public static function insertPerm($role_id, $perm_id) {

        global $db;

        $sql = "INSERT INTO role_perm (role_id, perm_id) VALUES (:role_id, :perm_id)";
        $sth = $db->prepare($sql);
        return $sth->execute(array(":role_id" => $role_id, ":perm_id" => $perm_id));
    }

    // delete ALL role permissions
    public static function deletePerms() {

        global $db;

        $sql = "TRUNCATE role_perm";
        $sth = $db->prepare($sql);
        return $sth->execute();
    }
}