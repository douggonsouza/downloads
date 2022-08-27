<?php

namespace douggonsouza\downloads;

abstract class upload
{
	const TYPES_IMG = 'img';
	const TYPES_ALL = 'all';
	const TYPES_TXT = 'txt';

	const TYPE_JPG = 'jpg';
	const TYPE_JPEG = 'jpeg';
	const TYPE_PNG = 'png';
	const TYPE_GIF = 'gif';
	const TYPE_BMP = 'bmp';

	const TYPE_TXT = 'txt';
	const TYPE_DOC = 'doc';
	const TYPE_DOCX = 'docx';
	const TYPE_PDF = 'pdf';
	const TYPE_CSV = 'csv';

    static public $listTypes = array(
        self::TYPE_TXT => self::TYPES_TXT,
		self::TYPE_DOC => self::TYPES_TXT,
		self::TYPE_DOCX => self::TYPES_TXT,
		self::TYPE_PDF => self::TYPES_TXT,
		self::TYPE_CSV => self::TYPES_TXT,
		self::TYPE_JPG => self::TYPES_IMG,
		self::TYPE_JPEG => self::TYPES_IMG,
		self::TYPE_PNG => self::TYPES_IMG,
		self::TYPE_GIF => self::TYPES_IMG,
		self::TYPE_BMP => self::TYPES_IMG
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
		if(!isset($type) || empty($type)){
            return null;
        }

		return time().$indice.'.'.$type;
	}

	public function save($pasta)
	{
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
			if(!$this->validType($tp)){
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

	private function validType($ext)
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
	
	/**
	 * getTipo
	 *
	 * @return void
	 */
	public function getTipo()
	{
		return $this->tipo;
	}
	
	/**
	 * setTipo
	 *
	 * @param  mixed $type
	 * @return void
	 */
	public function setTipo($type)
	{
		// inicia tipo
		if(isset($type) && !empty($type)){
			$this->tipo = $type;
		}
		
		$this->this;
	}
	
	/**
	 * defineType
	 *
	 * @param  mixed $type
	 * @return void
	 */
	private function defineType($type)
	{
		// inicia tipo
		if(!isset($type) || empty($type)){
			return null;
		}

		if(array_key_exists($type, self::$listTypes)){
			return self::$listTypes[$type];
		};
		
		return null;
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
			if (file_exists($pasta)) {
				$this->pasta = $pasta;
			}
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