<?php 
use \Firebase\JWT\JWT;

class Login_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function Login($user, $pass)
    {
        $output = array();
        $this->db->join('User', 'SuperUser.ID_SU = User.ID_USU', 'left');
        $this->db->where('SUname', $user);
        $this->db->where('pswd', md5($pass));
        $query = $this->db->get('SuperUser');
        
        if ($query->num_rows() == 0) {
            return false;
        }
            
        $output = $query->result();
        $tokenId    = base64_encode(mcrypt_create_iv(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt;                            // Is valid right away
        $expire     = $notBefore + (60 * 60  * 24 * 365 * 50);     // Token expires in 5 years
        $serverName = 'myserver'; // Retrieve the server name from config file
        
        /*
         * Create the token as an array
         */
        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userId'   => $output[0]->ID_SU, // userid from the users table
                'userName' => $output[0]->SUname // User name
            ]
        ];

        $secretKey = base64_decode('lamevaclausupersecreta');
        $jwt = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
            
        //Miramos el default language del Super User
        $this->db->select('cfgDefLanguage, languageabbr'); // Seleccionar les columnes
        $this->db->from('SuperUser');// Seleccionem la taula
        $this->db->join('Languages', 'SuperUser.cfgDefLanguage = Languages.ID_Language', 'left');
        $this->db->where('SUname', $user);// filtrem per columnes
        $query2 = $this->db->get()->result_array();// Fem la query i la guardem a la variable query2

        // Sacamos la variable language del array
        $languageid = array_column($query2, 'cfgDefLanguage');
        $languageabbr = array_column($query2, 'languageabbr');

        //Cojemos el id de usuario y superusuario


        // Guardamos los datos como objeto
        $unencodedArray = [
            'token' => $jwt,
            'languageid' => $languageid,
            'languageabbr' => $languageabbr,
            'userID' => $output[0]->ID_User
        ];

        return $unencodedArray;
    }

}