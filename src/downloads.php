</php

namespace douggonsouza\downloads;

abstract class upload
{
	const TYPES_IMG = 'img';
	const TYPES_ALL = 'all';
	const TYPES_TXT = 'txt';

    static public $listTypesImg = array(
        'TYPE_JPG' => 'jpg',
        'TYPE_JPEG' => 'jpeg',
        'TYPE_PNG' => 'png',
        'TYPE_GIF' => 'gif'
    );

    static public $listTypesTxt = array(
        'TYPE_TXT' => 'txt',
        'TYPE_DOC' => 'doc',
        'TYPE_DOCX' => 'docx',
        'TYPE_PDF' => 'pdf',
    );

	public $pasta          = '';
	public $tamanho        = 10485760;
	public $tipo           = 'all';
	public $nomeSequencial = true;
	public $Salvos         = array();
	public $Erros          = array();
	public $nomeArquivo   = '';
	public $files          = array();

	private function autoName($type = null, $indice = null)
	{
		if(isset($type) && strlen($type) > 0){
            return time().'_'.$indice.'.'.$type;
        }

		return NULL;
	}

	public function save($pasta)
	{
		<!-- $folder = $_ENV['FOLDERUPLOAD']; -->
		$this->setPasta($pasta);

        // pasta existe no sistema
		if(empty($this->getPasta())){
			$this->setFiles(array("Não existe pasta de salvamento."), 'error');
			return false;
		}

		//ARRAY $_FILES VAZIO
		if(!isset($_FILES) || empty($_FILES)){
			$this->setFiles(array("Não existem arquivos para upload."), 'error');
			return false;
		}

		$indice = 1;
		foreach($_FILES as $input => $arquivo){
			$arq = array();

			if(isset($this->tamanho) && $arquivo['size'] >= $this->tamanho){
				$arq = array('status' => 'false','mensagem' => "Ultrapassado tamanho máximo.");
				$this->setFiles($arq, $input);
				continue;
			}

			// existe extensão
			$tp = substr($arquivo['name'], strrpos($arquivo['name'],'.')+1,strlen($arquivo['name']));
			if(!isset($tp) || empty($tp)){
				$arq = array('status' => 'false','mensagem' => "Extensão não identificada.");
				$this->setFiles($arq, $input);
				continue;
			}

			//TIPO VálIDO
			if(!$this->tipoValido($tp)){
				$arq = array('status' => 'false','mensagem' => "Erro na validade do tipo de arquivo.");
				$this->setFiles($arq, $input);
				continue;
			}

			// define nome do arquivo
			$fileName = $this->autoName( $tp, $indice);
			if(!isset($fileName) || empty($fileName)){
				$arq = array('status' => 'false','mensagem' => "Não encontrado um nome para o arquivo.");
				$this->setFiles($arq, $input);
				continue;
			}

			//MOVER ARQUIVO
			if(!move_uploaded_file($arquivo['tmp_name'],  $this->pasta.'/'.$fileName)){
				$arq = array('status' => 'false','mensagem' => "Erro na transferência do arquivo.");
				$this->setFiles($arq, $input);
				continue;
			}

			//NOTIFICAR SALVAMENTO
			$arq = array('status' => 'true','mensagem' => $this->getPasta().'/'.$fileName);
			$this->setFiles($arq, $input);
			$indice++;
		}

		return $this->getFiles();
	}

	private function tipoValido($ext)
	{
		if(!isset($ext) || empty($ext)){
			return false;
		}

		$defType = $this->defineType($ext);

		if($this->tipo == 'all'){
			return true;
		}

		if($this->tipo == $defType){
			return true;
		}

		return false;
	}

	public function setType($type)
	{
		// inicia tipo
		$this->tipo = $this->defineType($type);
	}

	private function defineType($type)
	{
		// inicia tipo
		$tipos = null;
		switch($type){
			case "jpg": $tipos  = self::TYPES_IMG; break;
			case "jpeg": $tipos = self::TYPES_IMG; break;
			case "gif": $tipos  = self::TYPES_IMG; break;
			case "bmp": $tipos  = self::TYPES_IMG; break;
			case "png": $tipos  = self::TYPES_IMG; break;
			case "wmf": $tipos  = self::TYPES_IMG; break;
			case "txt": $tipos  = self::TYPES_TXT; break;
            case "doc": $tipos  = self::TYPES_TXT; break;
            case "docx": $tipos  = self::TYPES_TXT; break;
            case "pdf": $tipos  = self::TYPES_TXT; break;
			case "csv": $tipos  = self::TYPES_TXT; break;
			default: $tipos = null; break;
		}

		return $tipos;
	}
	

	/**
	 * Get the value of pasta
	 */ 
	public function getPasta()
	{
		return $this->pasta;
	}

	/**
	 * Set the value of pasta
	 *
	 * @return  self
	 */ 
	public function setPasta($pasta)
	{
		if(isset($pasta) && !empty($pasta)){
			$this->pasta = $pasta;
		}
		return $this;
	}

	/**
	 * Get the value of files
	 */ 
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Set the value of files
	 *
	 * @return  self
	 */ 
	public function setFiles(array $value,string $index)
	{
		if(isset($value) && !empty($value)){
			$this->files[$index] = $value;
		}
		return $this;
	}
}

?>