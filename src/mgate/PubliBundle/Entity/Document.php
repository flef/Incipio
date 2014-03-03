<?php
namespace mgate\PubliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    
    /**
     * @ORM\OneToOne(targetEntity="CategorieDocument", mappedBy="document", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;    
    
    /**
     * @var \DateTime $uptime
     *
     * @ORM\Column(name="uptime", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $uptime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
    
    public function getUpTime(){
        return $this->uptime;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        // store in /data/incipio as it's the place with disk free
        return 'C:/data/incipio/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'documents';
    }
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->file->guessExtension();
        }
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */    
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        // moving file into /data
        $this->file->move($this->getUploadRootDir(), $this->path);
        // creating symlink to acces file from web/...
        symlink ( $this->getUploadRootDir().'/'.$this->path, $this->getWebPath());
        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if($file = $this->getWebPath())
            unlink($file);
        if ($file = $this->getAbsolutePath())
            unlink($file);
    }        
    
    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uptime
     *
     * @param \DateTime $uptime
     * @return Document
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;
    
        return $this;
    }

    /**
     * Get uptime
     *
     * @return \DateTime 
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set categorie
     *
     * @param \mgate\PubliBundle\Entity\CategorieDocument $categorie
     * @return Document
     */
    public function setCategorie(\mgate\PubliBundle\Entity\CategorieDocument $categorie = null)
    {
        $this->categorie = $categorie;
    
        return $this;
    }

    /**
     * Get categorie
     *
     * @return \mgate\PubliBundle\Entity\CategorieDocument 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}