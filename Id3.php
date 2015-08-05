<?php
/**
 * Classe d'extraction et d'imputation de tags Id3 v3
 * @author Xylphid
 */
class Id3 extends GetSet
{
	const UNSYNCHRONISE = 0x800;
	const EXTENDED_HEADER = 0x400;
	const EXPERIMENTAL_INDICATOR = 0x200;
	const FOOTER = 0x100;
	
	protected $_compliant	=	false;
	protected $_headers;
	protected $_id3Version	=	null;
	protected $_mediaFile;
	protected $_properties	=	array();
	protected $_reader		=	null;
	
	private $id3v22 = array(
							'buf'	=>	'Taille recommandée du buffer',
							'cnt'	=>	'PlayCount',
							'cra'	=>	'Chiffrage audio',
							'crm'	=>	'Encrypted meta frame',
							'etc'	=>	'Event timing code',
							'equ'	=>	'Equalisation',
							'geo'	=>	'Objet d\'encapsulation général',
							'ipl'	=>	'Liste des personnes investies',
							'lnk'	=>	'Information',
							'mci'	=>	'MusicCDIdentifier',
							'mll'	=>	'MPEG location lookup table',
							'pic'	=>	'Picture',
							'pop'	=>	'Popularimeter',
							'rev'	=>	'Reverb',
							'rva'	=>	'VolumeAdjustment',
							'slt'	=>	'SynchronizedLyric',
							'stc'	=>	'Synced',
							'tal'	=>	'Album',
							'tbp'	=>	'BPM',
							'tcm'	=>	'Composer',
							'tco'	=>	'Genre',
							'tcr'	=>	'Copyright',
							'tda'	=>	'Date',
							'tdy'	=>	'Delay',
							'ten'	=>	'Encoded',
							'tft'	=>	'FileType',
							'tim'	=>	'EncodingTime',
							'tke'	=>	'InitialKey',
							'tla'	=>	'Language',
							'tle'	=>	'Length',
							'tmt'	=>	'MediaType',
							'toa'	=>	'OriginalArtist',
							'tof'	=>	'OriginalFilename',
							'tol'	=>	'OriginalLyricist',
							'tor'	=>	'OriginalRelease',
							'tot'	=>	'OriginalArtwork',
							'tp1'	=>	'LeadArtist',
							'tp2'	=>	'Band',
							'tp3'	=>	'Conductor',
							'tp4'	=>	'Interpreted, remixed, or otherwise modified by',
							'tpa'	=>	'PartOfSet',
							'tpb'	=>	'Publisher',
							'trc'	=>	'ISRC (International Standard Recording Code)',
							'trd'	=>	'Recording',
							'trk'	=>	'Track',
							'tsi'	=>	'Size',
							'tss'	=>	'Software/hardware and settings used for encoding',
							'tt1'	=>	'Content group description',
							'tt2'	=>	'Title',
							'tt3'	=>	'Subtitle',
							'txt'	=>	'Lyricist',
							'txx'	=>	'User defined text information frame',
							'tye'	=>	'Year',
							'ufi'	=>	'Uid',
							'ult'	=>	'UnsychronizedLyric',
							'waf'	=>	'Official audio file webpage',
							'war'	=>	'Official artist/performer webpage',
							'was'	=>	'Official audio source webpage',
							'wcm'	=>	'Commercial',
							'wcp'	=>	'Legal',
							'wpb'	=>	'Publishers official webpage',
							'wxx'	=>	'User defined URL link frame'
						);
	private $id3v23 = array(
							'aenc' => 'Audio encryption',
							'apic' => 'Picture',
							'aspi' => 'Audio seek point index',
							'comm' => 'Comments',
							'comr' => 'Commercial frame',
							'encr' => 'Encryption method registration',
							'equ2' => 'Equalisation',
							'etco' => 'Event timing codes',
							'geob' => 'General encapsulated object',
							'grid' => 'Group identification registration',
							'link' => 'Linked information',
							'mcid' => 'MusicCDIdentifier',
							'mllt' => 'MPEG location lookup table',
							'owne' => 'Ownership frame',
							'priv' => 'Private frame',
							'pcnt' => 'PlayCounter',
							'popm' => 'Popularimeter',
							'poss' => 'Position synchronisation frame',
							'rbuf' => 'Recommended buffer size',
							'rva2' => 'VolumeAdjustment',
							'rvrb' => 'Reverb',
							'seek' => 'Seek frame',
							'sign' => 'Signature frame',
							'sylt' => 'SynchronisedLyric',
							'sytc' => 'Synchronised tempo codes',
							'talb' => 'Album',
							'tbpm' => 'BPM',
							'tcom' => 'Composer',
							'tcon' => 'Genre',
							'tcop' => 'Copyright',
							'tden' => 'EncodingTime',
							'tdly' => 'Delay',
							'tdor' => 'OriginalRelease',
							'tdrc' => 'RecordingTime',
							'tdrl' => 'ReleaseTime',
							'tdtg' => 'Tagging time',
							'tenc' => 'Encoded',
							'text' => 'Lyricist',
							'tflt' => 'FileType',
							'tipl' => 'Involved people',
							'tit1' => 'Band',
							'tit2' => 'Title',
							'tit3' => 'Subtitle',
							'tkey' => 'InitialKey',
							'tlan' => 'Language',
							'tlen' => 'Length',
							'tmcl' => 'Musician credits list',
							'tmed' => 'MediaType',
							'tmoo' => 'Mood',
							'toal' => 'OriginalArtwork',
							'tofn' => 'OriginalFilename',
							'toly' => 'OriginalLyricist',
							'tope' => 'OriginalArtist',
							'town' => 'File owner/licensee',
							'tpe1' => 'LeadArtist',
							'tpe2' => 'Accompaniment',
							'tpe3' => 'Conductor/performer refinement',
							'tpe4' => 'Interpreted, remixed, or otherwise modified by',
							'tpos' => 'PartOfSet',
							'tpro' => 'Produced notice',
							'tpub' => 'Publisher',
							'trck' => 'Track',
							'trsn' => 'Internet radio station name',
							'trso' => 'Internet radio station owner',
							'tsoa' => 'Album sort order',
							'tsop' => 'Performer sort order',
							'tsot' => 'Title sort order',
							'tsrc' => 'ISRC (international standard recording code)',
							'tsse' => 'Software/Hardware and settings used for encoding',
							'tsst' => 'Set subtitle',
							'txxx' => 'User text information',
							'tyer' => 'Year',
							'ufid' => 'Unique file identifier',
							'user' => 'Terms of use',
							'uslt' => 'Unsynchronised lyric/text transcription',
							'wcom' => 'Commercial information',
							'wcop' => 'Copyright/Legal information',
							'woaf' => 'Official audio file webpage',
							'woar' => 'Official artist/performer webpage',
							'woas' => 'Official audio source webpage',
							'wors' => 'Official Internet radio station homepage',
							'wpay' => 'Payment',
							'wpub' => 'Publishers official webpage',
							'wxxx' => 'User defined URL link frame',
						);

    /**
     * Construteur
     * @param string $file Fichier à ouvrir
     * @throws Exception
     * @return void
     */
	public function __construct($file = null)
	{
		if ($file != null)
		{
			try {
				// Remote files not supported
				if (preg_match('/^(ht|f)tp:\/\//', $file))
				    throw new Exception(__METHOD__.' : Remote files are not supported - please copy the file locally first.');
				if (!file_exists($file))
					throw new Exception( __METHOD__ . ' : File not found.');
					
				$this->_mediaFile = $file;
				if (($this->_reader = fopen($this->_mediaFile, 'r')) === false)
					throw new Exception(__METHOD__ . ' : Unable to open file.');
				
				$this->extractHeaders();
				if ($this->_headers['signature'] == 'ID3')
				{
					$this->_compliant = true;
					$this->extractTags();
					ksort($this->_properties);
					fclose($this->_reader);
				}
			}
			catch (Exception $e) {
				trigger_error( $e->getMessage() );
			}
		}
	}
	
	/**
	 * Cleanup Id3 tags
	 * @return boolean
	 */
	protected function clean()
	{
		$fsize = filesize($this->_mediaFile);
		$content = file_get_contents($this->_mediaFile);
		$this->_reader = fopen($this->_mediaFile, 'w');
		fwrite($this->_reader, substr($content, $this->_headers['size']));
		fclose($this->_reader);
	}
	
	/**
	 * Effectue un dump de l'objet
	 * @return void
	 */
	public function debug()
	{
		var_dump($this);
		//print '<pre>' . print_r($this, true) . '</pre>';
	}

	/**
	 * Extract ID3 Headers
	 * @throws Exception
	 * @return boolean
	 */
	protected function extractHeaders()
	{
		try {
			$this->_headers = fread($this->_reader, 10);
			$this->_headers = unpack("a3signature/c1version_major/c1version_minor/c1flags/Nsize", $this->_headers);
			if ($this->_headers['signature'] != 'ID3')
				throw new Exception(__METHOD__ . ' : This file does not contain ID3 v2 tag');
			
			$this->_id3Version = sprintf('%d.%d', $this->_headers['version_major'], $this->_headers['version_minor']);
			if (($this->_headers['flags'] & self::UNSYNCHRONISE) == self::UNSYNCHRONISE)
				var_dump('UNSYNCHROSINE');
			if (($this->_headers['flags'] & self::EXTENDED_HEADER) == self::EXTENDED_HEADER)
				var_dump('EXTENDED_HEADER');
			if (($this->_headers['flags'] & self::EXPERIMENTAL_INDICATOR) == self::EXPERIMENTAL_INDICATOR)
				var_dump('EXPERIMENTAL_INDICATOR');
			if (($this->_headers['flags'] & self::FOOTER) == self::FOOTER)
				var_dump('FOOTER');
			return true;
		}
		catch (Exception $e) {
			trigger_error($e->getMessage());
			return false;
		}
	}
	
	/**
	 * Extract ID3 tags
	 * throws Exception
	 * @return boolean
	 */
	public function extractTags()
	{
		try {
			$options = array(
				'reader'	=> $this->_reader,
				'size'		=> $this->_headers['size'],
				'version'	=> $this->_headers['version_major']
			);
			
			while (!feof($this->_reader) && ftell($this->_reader) < $this->_headers['size'])
			{
				$frameSize = ($this->_headers['version_major'] == 2) ? 3 : 4;
				$haystack = ($this->_headers['version_major'] == 2) ? $this->id3v22 : $this->id3v23;
				$tag = strtolower(preg_replace('/[^A-Z0-9]/', '', fread($this->_reader, $frameSize)));
				// -- If tag is not registred, move pointer and go next
				if (!array_key_exists($tag, $haystack))
					continue;

				// -- Define Tag class and Getter/Setter
				$frameClass = ucfirst($tag);
				$setter = 'set' . $frameClass;
				$getter = 'get' . $frameClass;
				// -- Define Tag name
				$options['tagname'] = $tag;
				if (array_key_exists($tag, $this->_properties) && !($this->_properties[$tag] instanceof Collection)){
					$this->$setter(new Collection($this->$getter()));
				}
				
				$genericFrame = ($this->_headers['version_major'] == 2) ? 'UnknownFrame' : 'TextFrame';
				if (!array_key_exists($tag, $this->_properties))
					$this->$setter((class_exists($frameClass)) ? new $frameClass($options) : new $genericFrame($options));
				else
					$this->$getter()->add((class_exists($frameClass)) ? new $frameClass($options) : new TextFrame($options));
				if ($tag == 'mci')
					var_dump($this->$getter());
			}
			return true;
		}
		catch (Exception $e) {
			trigger_error($e->getMessage());
			return false;
		}
	}
	
	public function getSize()
	{
		$size = 10;
		foreach ($this->_properties as $tag => $obj) {
			if ($obj instanceof Collection) {
				foreach ($obj as $item)
					$size += $item->getSize() + 10;
			}
			else
				$size += $obj->getSize() + 10;
		}
		return $size;
	}
	
	/**
	 * Get Id3 version
	 * @return int
	 */
	public function getVersion()
	{
		return $this->_id3Version;
	}
	
	/**
	 * Tell if file is ID3 compliant
	 * @return boolean
	 */
	public function isCompliant()
	{
		return $this->_compliant;
	}
	
	/**
	 * Update file with new Id3 tags
	 */
	public function save()
	{
		$this->clean();
		$buffer = pack('a3cccN', $this->_headers['signature'], $this->_headers['version_major'], $this->_headers['version_minor'], $this->_headers['flags'], $this->getSize());
		foreach ($this->_properties as $tag => $obj) {
			if ($obj instanceof Collection) {
				foreach ($obj as $item)
					$buffer .= strtoupper($tag) . $item->write();
			}
			else
				$buffer .= strtoupper($tag) . $obj->write();
		}
		$this->_reader = fopen($this->_mediaFile, 'rb+');
		fwrite($this->_reader, $buffer);
		fclose($this->_reader);
	}
	
	protected function _get($property)
	{
		if (property_exists($this, $property) || array_key_exists($property, $this->_properties))
			return parent::_get($property);
			
		$haystack = ($this->_headers['version_major'] == 2) ? $this->id3v22 : $this->id3v23;
		
		$property = array_search(ucfirst($property), $haystack);
		if ($property && property_exists($this, $property)) {
			return $this->$property;
		}
		elseif($property && array_key_exists($property, $this->_properties)) {
			return $this->_properties[$property];
		}
		else {
			trigger_error('Method "get' . ucfirst($property) . '()" does not exist in "' . get_class( $this ) . '" class.');
		}
	}
	protected function _has($property) {
		if (property_exists($this, $property) || array_key_exists($property, $this->_properties))
			return parent::_has($property);
			
		$haystack = ($this->_headers['version_major'] == 2) ? $this->id3v22 : $this->id3v23;
		
		$property = array_search(ucfirst($property), $haystack);
		if ($property && (property_exists($this, $property) || array_key_exists($property, $this->_properties)))
			return true;
		else
			return false;
	}
}
?>
