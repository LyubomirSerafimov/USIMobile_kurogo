<?php
error_reporting(E_ERROR);

class USIdb {

	private $config;

    public function __construct($config=null) {
        if (!is_array($config) || empty($config)) {
            if (!$config instanceOf Config) {
                $config = Kurogo::siteConfig();
            }
    
            $this->config = array(
                'DB_HOST'=>$config->getVar('DB_HOST', 'usi_database'),
                'DB_USER'=>$config->getVar('DB_USER', 'usi_database'),
                'DB_PASS'=>$config->getVar('DB_PASS', 'usi_database'),
                'DB_DBNAME'=>$config->getVar('DB_DBNAME', 'usi_database'),   
            );
        }
    }

	public function query($sql) {
		$connection = mssql_connect($this->config['DB_HOST'], $this->config['DB_USER'], $this->config['DB_PASS']);

		if($connection != false) {
			
			if(mssql_select_db($this->config['DB_DBNAME'], $connection)) {
				$query_result = mssql_query($sql);
				$result = array();
				while($row = mssql_fetch_assoc($query_result)){
					$result[] = $row;
				}
				return $result;
			} else {
				return false;
			}
		
		} else {
			return false;
		}
	}

	public function lastMessage() { return mssql_get_last_message(); }

	public function getCourses(){
		$sql = "select ";
		$sql.= "Titolo_corso as title, ";
		$sql.= "Descrizione_corso as description, ";
		$sql.= "Docente as professor, ";
		$sql.= "Facolta as faculty, ";
		$sql.= "Tipo_corso as level, ";
		$sql.= "Semestre as semester, ";
		$sql.= "convert(varchar(11), Inizio_semestre, 106) as semester_begin, ";
		$sql.= "convert(varchar(11), Fine_semestre, 106) as semester_end, ";
		$sql.= "Crediti as credits ";
		$sql.= "from Corsi";
		$usidb = new USIdb();
		$result = $usidb->query($sql);
		if($result == false) {
			return $this->error(0);
		}
		return $result;
	}

	public function getCoursesHash(){
		$sql = "select convert(binary(4), checksum_agg(checksum(*))) from Corsi";
		$usidb = new USIdb();
		$result = $usidb->query($sql);
		if($result == false) {
			return $this->error(1);
		}
		return md5($result[0]['computed']);
	}

	public function getPeople(){
		$sql = "select ";
		$sql.= "id_persona as id, ";
		$sql.= "cognome as lastname, ";
		$sql.= "nome as firstname, ";
		$sql.= "email, ";
		$sql.= "nr_tel as phone, ";
		$sql.= "Locale_Piano as floor ";
		$sql.= "from Persone_Ruoli_Uffici";
		$usidb = new USIdb();
		$result = $usidb->query($sql);
		if($result == false) {
			return $this->error(2);
		}
		return $result;
	}

	public function getPeopleHash(){
		$sql = "select convert(binary(4), checksum_agg(checksum(*))) from Persone_Ruoli_Uffici";
		$usidb = new USIdb();
		$result = $usidb->query($sql);
		if($result == false) {
			return $this->error(3);
		}
		return md5($result[0]['computed']);
	}

	public function getSportActivities(){
		$sql = "select ";
		$sql.= "nome_attivita as name_it, ";
		$sql.= "nome_attivita_eng as name_en, ";
		$sql.= "descrizione_attivita as description_it, ";
		$sql.= "descrizione_attivita_eng as description_en, ";
		$sql.= "Categoria as category_it, ";
		$sql.= "Categoria_eng as category_en, ";
		$sql.= "Disciplina as discipline_it, ";
		$sql.= "Disciplina_eng as discipline_en, ";
		$sql.= "convert(varchar(11), data_inizio, 106) as start_date, ";
		$sql.= "convert(varchar(11), data_fine, 106) as end_date, ";
		$sql.= "convert(varchar(11), data_inizio_iscrizione, 106) as subscription_start_date, ";
		$sql.= "convert(varchar(11), data_fine_iscrizione, 106) as subscription_end_date, ";
		$sql.= "luogo as location, ";
		$sql.= "alloggio as lodging_it, ";
		$sql.= "alloggio_eng as lodging_en, ";
		$sql.= "rientro as comeback, ";
		$sql.= "nome_contatto as contact_name, ";
		$sql.= "mail_contatto as contact_mail, ";
		$sql.= "trasporto as transport_it, ";
		$sql.= "trasporto_eng as transport_en, ";
		$sql.= "requisiti as requirements_it, ";
		$sql.= "requisiti_eng as requirements_en, ";
		$sql.= "equipaggiamento as equipment_it, ";
		$sql.= "equipaggiamento_eng as equipment_en, ";
		$sql.= "pasti as meal_it, ";
		$sql.= "pasti_eng as meal_en ";
		$sql.= "from AttivitaSport";
		$usidb = new USIdb();
		$result = $usidb->query($sql);
		if($result == false) {
			return $this->error(4);
		}
		return $result;
	}

	public function getSportActivitiesHash(){
		$sql = "select convert(binary(4), checksum_agg(checksum(*))) from AttivitaSport";
		$usidb = new USIdb();
		$result = $usidb->query($sql);
		if($result == false) {
			return $this->error(5);
		}
		return md5($result[0]['computed']);
	}

	public function error($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'USIdb: Getting Courses';
				$error->message = 'Getting Courses data failed: ' . mssql_get_last_message();
				break;
			case 1:
				$error->title = 'USIdb: Getting Courses Checksum';
				$error->message = 'Getting Courses checksum failed: ' . mssql_get_last_message();
				break;
			case 2:
				$error->title = 'USIdb: Getting People';
				$error->message = 'Getting People data failed: ' . mssql_get_last_message();
				break;
			case 3:
				$error->title = 'USIdb: Getting People Checksum';
				$error->message = 'Getting People checksum failed: ' . mssql_get_last_message();
				break;
			case 4:
				$error->title = 'USIdb: Getting Sport Activities';
				$error->message = 'Getting Sport Activities data failed: ' . mssql_get_last_message();
				break;
			case 5:
				$error->title = 'USIdb: Getting Sport Activities Checksum';
				$error->message = 'Getting Sport Activities checksum failed: ' . mssql_get_last_message();
				break;
			default:
				$error->title = 'USIdb: Unknown error';
				$error->message = 'Unknown error. Last db message:' . mssql_get_last_message();
		}
		return $error;
	}

}
