<?php
	ob_start();

	inicialize();
	protectFile(basename(__FILE__));
	date_default_timezone_set("Atlantic/Cape_Verde");

	//php error report
	//require('classes/php_error.php');
	//php_error\reportErrors( array('display_line_numbers' => true) );

	function inicialize() {
		if (file_exists(dirname(__FILE__) . '/config.php')) {
			require_once (dirname(__FILE__) . '/config.php');
		} else {
			die('The configuration file was not found, contact the administrator.');
		}
		$constant = array("BASEPATH", "BASEURL", "CLASSESPATH", "CSSPATH", "JSPATH", "ENVIRONMENT", "DBHOST", "DBUSER", "DBPASS", "DBNAME");
		foreach ($constant as $value) {
			if (!defined($value)) {
				die("A basic system configuration is missing: " . $value . ", contact the administrator.");
			}
		}
		require_once (BASEPATH . CLASSESPATH . 'autoload.php');
		if (@$_GET['logoff'] == TRUE) {
			$user = new users();
			$user->doLogout();
			exit;
		}//logoff
	}

	function loadCSS($file = NULL, $media = 'screen', $import = FALSE) {
		if ($file != NULL) {
			if ($import == TRUE) {
				echo '<style type="text/css">@import url("' . BASEURL . CSSPATH . $file . '.css");</style>' . "\n";
			} else {
				echo '<link rel="stylesheet" type="text/css" href="' . BASEURL . CSSPATH . $file . '.css" media="' . $media . '"/>' . "\n";
			}
		}
	}

	function loadJS($file = NULL, $remote = FALSE) {
		if ($file != NULL) {
			if ($remote == FALSE) {
				$file = BASEURL . JSPATH . $file . '.js';
				echo '<script type="text/javascript" src="' . $file . '"></script>' . "\n";
			}
		}
	}

	function loadModules($modules = NULL, $screen = NULL) {
		if ($modules == NULL || $screen == NULL) {
			printMSG('Erro na função <strong>' . __FUNCTION__ . '</strong>: missing parameters for execution.', 'danger');
		} else {
			if (file_exists(MODULESPATH . "$modules.php")) {
				include_once (MODULESPATH . "$modules.php");
			} else {
				send_404();
			}
		}
	}

	function protectFile($fileName, $redirectFor = 'index.php?error=3') {
		$url = $_SERVER["PHP_SELF"];
		if (preg_match("/$fileName/i", $url)) {
			redirect($redirectFor);
		}
	}

	function redirect($url = '') {
		header("Location: " . BASEURL . $url);
	}

	function encryptPass($password) {
		return md5($password);
	}

	function verifyLogin() {
		$session = new session();
		if ($session->getNvars() <= 0 || $session->getVar('login') != TRUE || $session->getVar('ip') != $_SERVER['REMOTE_ADDR']) {
			redirect('?error=3');
		}
	}

	function printMSG($msg = NULL, $type = NULL) {
		if ($msg != NULL) {
			switch ($type) {
				case 'danger':
					echo '<div class="alert alert-danger fresh-color alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong><i class="fa fa-times-circle"></i>&nbsp;Caution!</strong>&nbsp;' . $msg . '
					</div>';
					break;
				case 'warning':
					echo '<div class="alert alert-warning fresh-color alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong><i class="fa fa-exclamation-circle"></i>&nbsp;Attention!</strong>&nbsp;' . $msg . '
					</div>';
					break;
				case 'info':
					echo '<div class="alert alert-info fresh-color alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong><i class="fa fa-info-circle"></i>&nbsp;Information!</strong>&nbsp;' . $msg . '
					</div>';
					break;
				case 'success':
					echo '<div class="alert alert-success fresh-color alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong><i class="fa fa-check-circle"></i>&nbsp;Success!</strong>&nbsp;' . $msg . '
					</div>';
					break;
				default:
					echo '<div class="alert alert-success fresh-color alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<strong><i class="fa fa-check-circle"></i>&nbsp;Success!</strong>&nbsp;' . $msg . '
					</div>';
					break;
			}
		}
	}

	function DiaExtensao() {
		$dsemana = date('w');
		$semana[0] = 'Domingo';
		$semana[1] = 'Segunda';
		$semana[2] = 'Terça';
		$semana[3] = 'Quarta';
		$semana[4] = 'Quinta';
		$semana[5] = 'Sexta';
		$semana[6] = 'Sábado';
		echo $semana[$dsemana];
	}

	function DataExtensao() {
		$ano = date('Y');
		$dia = date('d') - 0;
		$data = date('n');
		$mes[1] = 'Janeiro';
		$mes[2] = 'Fevereiro';
		$mes[3] = 'Março';
		$mes[4] = 'Abril';
		$mes[5] = 'Maio';
		$mes[6] = 'Junho';
		$mes[7] = 'Julho';
		$mes[8] = 'Agosto';
		$mes[9] = 'Setembro';
		$mes[10] = 'Outubro';
		$mes[11] = 'Novembro';
		$mes[12] = 'Dezembro';
		echo $dia . ' de ' . $mes[$data] . ' de ' . $ano;
	}

	function antiInject($string) {
		// remove palavras que contenham sintaxe sql
		$string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i", "", $string);
		$string = trim($string); //limpa espaços vazio
		$string = strip_tags($string); //tira tags html e php
		if (!get_magic_quotes_gpc()) {
			$string = addslashes($string);
		} //Adiciona barras invertidas a uma string
		return $string;
	}

	function slug($text) {
		//replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);
		//transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		//remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);
		//trim
		$text = trim($text, '-');
		//remove duplicate -
		$text = preg_replace('~-+~', '-', $text);
		//lowercase
		$text = strtolower($text);
		if (empty($text)) {
			return 'N/A';
		}
		return $text;
	}

	//combobox
	function createCombo($table, $name_combo, $n_column) {

		$conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$conn->query("SET NAMES 'utf8'");
		if (mysqli_connect_errno()) {
			printf("Connection failed: %s\n", mysqli_connect_error());
			exit();
		}

		$select = $conn->query("SELECT * FROM $table");
		if (!$select) {
			die('Query failed: ' . mysqli_error());
		}
		$i = 0;
		while ($row = mysqli_fetch_array($select)) {
			while ($i < mysqli_num_fields($select)) {
				$meta = mysqli_fetch_field($select);
				if (!$meta) {
					echo "No information available<br />\n";
				}
				if ($i == 0) {
					$num = $meta->name;
				}
				if ($i == $n_column) {
					$column = $meta->name;
				}
				$i++;
			}
		}

		$combo = antiInject(@$_POST[$name_combo]);

		$query = $conn->query("SELECT * FROM $table");
		if (!$query) {
			echo "Failed to compile list";
		}
		?>
		<select class="selectpicker show-tick form-control" id="<?php echo $name_combo ?>" data-live-search="true" name="<?php echo $name_combo ?>" required="required">
			<option value="" data-hidden="true">-- Escolhe opção --</option>
			<?php
			while ($line = mysqli_fetch_array($query)) {
				if ($line[$num] == $combo) {
					?>
					<option selected value="<?php echo $line[$num]; ?>"><?php echo $line[$column]; ?></option>;
					<?php
				} else {
					?>
					<option value="<?php echo $line[$num]; ?>"><?php echo $line[$column]; ?></option>;
					<?php
				}
			}
			?>
		</select>
		<?php
	}

	function conditionCombo($table, $name_combo, $n_column, $where = "", $order = "") {

		$conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$conn->query("SET NAMES 'utf8'");
		if (mysqli_connect_errno()) {
			printf("Connection failed: %s\n", mysqli_connect_error());
			exit();
		}

		if ($where != "") {
			$where = "WHERE $where";
		}
		if ($order != "") {
			$order = "ORDER BY $order";
		}
		$select = $conn->query("SELECT * FROM $table $where $order");

		$i = 0;
		while ($row = mysqli_fetch_array($select)) {
			while ($i < mysqli_num_fields($select)) {
				$meta = mysqli_fetch_field($select);
				if (!$meta) {
					echo "No information available<br />\n";
				}
				if ($i == 0) {
					$num = $meta->name;
				}
				if ($i == $n_column) {
					$column = $meta->name;
				}
				$i++;
			}
		}

		$combo = antiInject(@$_POST[$name_combo]);

		$query = $conn->query("SELECT * FROM $table $where $order");
		if (!$query) {
			echo "Failed to compile list";
		}
		?>
		<select class="selectpicker show-tick form-control" id="<?php echo $name_combo ?>" data-live-search="true" name="<?php echo $name_combo ?>" required="required">
			<option value="" data-hidden="true">-- Escolhe opção --</option>
			<?php
			while ($line = mysqli_fetch_array($query)) {
				if ($line[$num] == $combo) {
					?>
					<option selected value="<?php echo $line[$num]; ?>"><?php echo $line[$column]; ?></option>;
					<?php
				} else {
					?>
					<option value="<?php echo $line[$num]; ?>"><?php echo $line[$column]; ?></option>;
					<?php
				}
			}
			?>
		</select>
		<?php
	}

	//edit combo
	function edit_Combo($tabela, $nome_combo, $n_column, $last_option) {

		$conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
		$conn->query("SET NAMES 'utf8'");
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		$query3 = "SELECT * FROM $tabela";
		$result = $conn->query($query3);
		if (!$result) {
			die('Query failed: ' . mysqli_error());
		}

		$i = 0;
		while ($i < mysqli_num_fields($result)) {
			$meta = mysqli_fetch_field($result);
			if (!$meta) {
				echo "No information available<br />\n";
			}
			if ($i == 0) {
				@$teste1 = $meta->name;
			}
			if ($i == $n_column) {
				$teste2 = $meta->name;
			}
			$i++;
		}
		mysqli_free_result($result);

		$query1 = "SELECT * FROM $tabela WHERE $teste1 = $last_option"; //seleciona o dado igual ao escolhido anteriormente
		$res1 = $conn->query($query1);
		$query2 = "SELECT * FROM $tabela WHERE $teste1 != $last_option"; //seleciona os dados diferentes ao escolhido anteriormente
		$res2 = $conn->query($query2);
		?>
		<select class="selectpicker show-tick form-control" data-live-search="true" id="<?php echo $nome_combo ?>" name="<?php echo $nome_combo ?>" required="required">
			<?php
			while ($linha1 = mysqli_fetch_array($res1)) {
				?>
				<option value="<?php echo $linha1["$teste1"]; ?>"><?php echo $linha1["$teste2"]; ?></option>;
				<?php
			}
			while ($linha2 = mysqli_fetch_array($res2)) {
				?>
				<option value="<?php echo $linha2["$teste1"]; ?>"><?php echo $linha2["$teste2"]; ?></option>;
				<?php
			}
			?>
		</select>

		<?php
	}

	//cut text
	function shortdata($string, $len) {
		$i = $len;
		while ($i < strlen($string)) {
			if ($string[$i] == ' ') {
				$string = substr($string, 0, $i) . "...";
				return $string;
			}
			$i++;
		}
		return $string;
	}

	//database backup
	function exportTable($host, $user, $pass, $name, $tables = false, $backup_name = false) {
		$mysqli = new mysqli($host, $user, $pass, $name);
		$mysqli->select_db($name);
		$mysqli->query("SET NAMES 'utf8'");
		$queryTables = $mysqli->query('SHOW TABLES');
		while ($row = $queryTables->fetch_row()) {
			$target_tables[] = $row[0];
		}
		if ($tables !== false) {
			$target_tables = array_intersect($target_tables, $tables);
		}

		$content = '--';
		$content .= "\n" . '-- Banco Dados: ' . "`$name`";
		$content .= "\n" . '--' . "\n";


		$content .= "\n" . 'CREATE DATABASE IF NOT EXISTS ' . $name . ";\n";
		$content .= 'USE ' . $name . ";\n\n";
		$content .="-- --------------------------------------------------------\n\n";

		foreach ($target_tables as $table) {
			$result = $mysqli->query('SELECT * FROM ' . $table);
			$fields_amount = $result->field_count;
			$rows_num = $mysqli->affected_rows;
			$content .= 'DROP TABLE IF EXISTS ' . $table . ';';
			$res = $mysqli->query('SHOW CREATE TABLE ' . $table);
			$TableMLine = $res->fetch_row();
			$content = (!isset($content) ? '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";
			for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
				while ($row = $result->fetch_row()) {
					if ($st_counter % 100 == 0 || $st_counter == 0) {
						$content .= "INSERT INTO " . $table . " VALUES";
					}
					$content .= "\n(";
					for ($j = 0; $j < $fields_amount; $j++) {
						$row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
						if (isset($row[$j])) {
							$content .= '"' . $row[$j] . '"';
						} else {
							$content .= '""';
						} if ($j < ($fields_amount - 1)) {
							$content.= ',';
						}
					}
					$content .=")";
					//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
					if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
						$content .= ";";
					} else {
						$content .= ",";
					} $st_counter = $st_counter + 1;
				}
			}
			$content .="\n\n-- -----------------------------------------------------------------------------------------";
			$content .="\n\n";
		}

		date_default_timezone_set("Atlantic/Cape_Verde");

		$backup_name = $backup_name ? $backup_name : $name . "(" . date('d-m-Y') . "__" . date('H.i.s') . ").sql";
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $backup_name . "\"");
		echo $content;
		exit;
	}

	function timeAgo($time_ago) {
		$cur_time = time();
		$time_elapsed = $cur_time - $time_ago;
		$seconds = $time_elapsed;
		$minutes = round($time_elapsed / 60);
		$hours = round($time_elapsed / 3600);
		$days = round($time_elapsed / 86400);
		$weeks = round($time_elapsed / 604800);
		$months = round($time_elapsed / 2600640);
		$years = round($time_elapsed / 31207680);
		// Seconds
		if ($seconds <= 60) {
			echo "há $seconds segundos";
		}
		//Minutes
		else if ($minutes <= 60) {
			if ($minutes == 1) {
				echo "há um minuto";
			} else {
				echo "há $minutes minutos";
			}
		}
		//Hours
		else if ($hours <= 24) {
			if ($hours == 1) {
				echo "há uma hora";
			} else {
				echo "há $hours horas";
			}
		}
		//Days
		else if ($days <= 7) {
			if ($days == 1) {
				echo "ontem";
			} else {
				echo "há $days dias";
			}
		}
		//Weeks
		else if ($weeks <= 4.3) {
			if ($weeks == 1) {
				echo "há uma semana";
			} else {
				echo "há $weeks semanas";
			}
		}
		//Months
		else if ($months <= 12) {
			if ($months == 1) {
				echo "há um mes";
			} else {
				echo "há $months meses";
			}
		}
		//Years
		else {
			if ($years == 1) {
				echo "há um ano";
			} else {
				echo "há $years anos";
			}
		}
	}

	//get youtube ID
	function get_youtube_id($url) {
		preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
		return @$matches[0];
	}

	//get vimeo ID
	function get_vimeo_id($url) {
		preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $matches);
		return @$matches[3];
	}

	//validate youtube id
	function validate_youtube_id($id) {
		$theURL = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$id&format=json";
		@$headers = get_headers($theURL);

		if (substr($headers[0], 9, 3) !== "404") {
			return true;
		} else {
			return false;
		}
	}

	//validate vimeo id
	function validate_vimeo_id($id) {
		$theURL = "https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/$id";
		$headers = get_headers($theURL);

		if (substr($headers[0], 9, 3) !== "404") {
			return true;
		} else {
			return false;
		}
	}

	//sanitize input
	function cleanMe($input) {
		$input = mysql_real_escape_string($input);
		$input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
		$input = strip_tags($input);
		$input = stripslashes($input);
		return $input;
	}

	//detect browser language
	function get_client_language($availableLanguages, $default = 'pt') {
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

			foreach ($langs as $value) {
				$choice = substr($value, 0, 2);
				if (in_array($choice, $availableLanguages)) {
					return $choice;
				}
			}
		}
		return $default;
	}

	//auto pagination
	function pagination($item_count, $limit, $cur_page, $link) {

		$page_count = ceil($item_count / $limit);
		$current_range = array(($cur_page - 2 < 1 ? 1 : $cur_page - 2), ($cur_page + 2 > $page_count ? $page_count : $cur_page + 2));

		// First and Last pages
		$first_page = $cur_page > 3 ? '<a href="' . sprintf($link, '1') . '">1</a>' . ($cur_page < 5 ? ', ' : ' ... ') : null;
		$last_page = $cur_page < $page_count - 2 ? ($cur_page > $page_count - 4 ? ', ' : ' ... ') . '<a href="' . sprintf($link, $page_count) . '">' . $page_count . '</a>' : null;

		// Previous and next page
		$previous_page = $cur_page > 1 ? '<a href="' . sprintf($link, ($cur_page - 1)) . '">Previous</a> | ' : null;
		$next_page = $cur_page < $page_count ? ' | <a href="' . sprintf($link, ($cur_page + 1)) . '">Next</a>' : null;

		// Display pages that are in range
		for ($x = $current_range[0]; $x <= $current_range[1]; ++$x) {
			$pages[] = '<a href="' . sprintf($link, $x) . '">' . ($x == $cur_page ? '<strong>' . $x . '</strong>' : $x) . '</a>';
		}

		if ($page_count > 1) {
			return '<p class="pagination"><strong>Pages:</strong> ' . $previous_page . $first_page . implode(', ', $pages) . $last_page . $next_page . '</p>';
		}
	}

	//custom 404 error - not found
	function send_404() {
		echo '<!DOCTYPE html>' .
		'<html lang="pt"><head>' .
		'<meta charset="UTF-8">' .
		'<title>404 Not Found</title>' .
		'</head><body>' .
		'<div class="container">' .
		'<div class="row content">' .
		'<div class="col-lg-12"></div>' .
		'<div class="col-lg-12">' .
		'<h2 style="font-size: 13rem; font-weight: 700; margin: 2% 0 2% 0; text-shadow: 0px 3px 0px #7f8c8d;">404</h2>' .
		'<p style="margin: -60px 0 2% 0; font-size: 7.4rem; text-shadow: 0px 3px 0px #7f8c8d; font-weight: 100;">ERROR</p>' .
		'<h2 style="margin-top: -25px;">Oops, the page you&rsquo;re looking for doesn&rsquo;t exist.</h2>' .
		'<p>' .
		'You may want to head back where you was before or go to the dashboard.' .
		'<br>' .
		'If you think something is broken, report the issue to the administrator (PM or Email).' .
		'</br>' .
		'</p>' .
		'<a href="./" title="Dashboard" class="btn btn-info">RETURN DASHBOARD</a>&nbsp;&nbsp;' .
		'<a href="?m=about&t=info" title="Suporte" class="btn btn-danger">REPORT PROBLEM</a>' .
		'</div>' .
		'</div>' .
		'</div>' .
		'</body></html>';
	}

	//custom 403 error - forbidden
	function send_403() {
		echo '<!DOCTYPE html>' .
		'<html lang="pt"><head>' .
		'<meta charset="UTF-8">' .
		'<title>403 Access Denied</title>' .
		'</head><body>' .
		'<div class="container">' .
		'<div class="row content">' .
		'<div class="col-lg-12"></div>' .
		'<div class="col-lg-12">' .
		'<h2 style="font-size: 13rem; font-weight: 700; margin: 2% 0 2% 0; text-shadow: 0px 3px 0px #7f8c8d;">403</h2>' .
		'<p style="margin: -60px 0 2% 0; font-size: 7.4rem; text-shadow: 0px 3px 0px #7f8c8d; font-weight: 100;">ERROR</p>' .
		'<h2 style="margin-top: -25px;">Hei, You don&rsquo;t have permission to access this page.</h2>' .
		'<p>' .
		'You may want to head back where you was before or go to the dashboard.' .
		'<br>' .
		'If you think something is broken, report the issue to the administrator (PM or Email).' .
		'</br>' .
		'</p>' .
		'<a href="./" title="Dashboard" class="btn btn-info">RETURN DASHBOARD</a>&nbsp;&nbsp;' .
		'<a href="?m=about&t=info" title="Suporte" class="btn btn-danger">REPORT PROBLEM</a>' .
		'</div>' .
		'</div>' .
		'</div>' .
		'</body></html>';
	}

	/*
	 * Gravatars
	 * @email - Email address to show gravatar for
	 * @size - size of gravatar
	 * @default - URL of default gravatar to use
	 * @rating - rating of Gravatar(G, PG, R, X)
	 */

	function show_gravatar($email, $size, $default, $rating) {
		echo '<img src="http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) .
		'&default=' . $default . '&size=' . $size . '&rating=' . $rating . '" width="' . $size . 'px" height="' . $size . 'px" />';
	}

	function maintenanceMode($status, $duration, $allowed) {

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$whois = preg_replace('/\s+/', '', $allowed);
		$array = explode(',', $whois);

		if ($status == 'on' && $duration > date('Y-m-d') && !in_array($ip, $array)) {
			if (basename($_SERVER['SCRIPT_FILENAME']) != 'maintenance.php') {
				$user = new users();
				$user->doLogout();
				redirect(MAINTENANCEPATH);
				exit;
			}
		} else {
			if (basename($_SERVER['SCRIPT_FILENAME']) == 'maintenance.php') {
				redirect();
			}
		}
	}

	function iframe($src, $width, $height) {
		$iframe = '<iframe src="' . $src . '" width="' . $width . '" height="' . $height . '" scrolling="no" allowtransparency="yes" frameborder="0" ></iframe>';
		return $iframe;
	}

	function blockIP($deny) {
		if (in_array($_SERVER['REMOTE_ADDR'], $deny)) {
			redirect('linkAqui');
			exit();
		}
	}

	function loopCrumb() {
		$url_string = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$url_explode = explode("/", $url_string);
		$i = 1;
		$count = count($url_explode);

		foreach ($url_explode as $value) {
			if ($value != "") {
				$array = array_slice($url_explode, 0, $i);
				$url = implode("/", $array);
				$title = str_replace("-", " ", $value);
				$title = ucwords($title);
				$title = ucwords(strtolower($title));
				if ($i == 1) {
					$title = "Home";
				}
				if ($title == 'Dashboard.php') {
					$title = pathinfo($title, PATHINFO_FILENAME);
				}
				if (strpos($title, '?m=') == true) {
					$title = substr($title, 16);
					$title = ucwords(str_replace('&t=', ' - ', $title));
				}
				if (strpos($title, '&id=') == true) {
					$title = ucwords(str_replace('&id=', ' ', $title));
					$title = preg_replace('/[0-9]+/', '', $title);
				}
				$crumb = '<li><a href="http://' . $url . '">' . $title . '</a></li>';
				echo $crumb;
				$i++;
			}
		}
	}

	function fbLikeCount($id,$appid,$appsecret){
		$json_url ='https://graph.facebook.com/'.$id.'?access_token='.$appid.'|'.$appsecret.'&fields=name,fan_count,link';
		$json = file_get_contents($json_url);
		$json_output = json_decode($json);
		//Extract the likes count from the JSON object
		if($json_output){
			return $json_output;
		}else{
			return 0;
		}
	}

	function getAlbunsfb($id,$access_token){
		//Get Albums
		$fields = "id,count,cover_photo,created_time,description,link,name";
		$json_link = "https://graph.facebook.com/".$id."/albums/?access_token=".$access_token."&fields=".$fields;
		$json = json_decode(file_get_contents($json_link));
		?>
		<select class="selectpicker form-control" id="album" data-live-search="true" name="album" required="required">
			<option value="" data-hidden="true">-- Escolhe opção --</option>
			<?php
			for($i=1; $i<= sizeof($json->data) ; $i++){
				$album = $json->data[$i-1];
				?>
					<option value="<?= $album->id; ?>"><?= $album->name; ?></option>
				<?php
			}
			?>
		</select>
		<?php
	}

	function prettyPrint($a) {
		echo '<pre>'.print_r($a,1).'</pre>';
	}

	ob_clean();
?>
