<?php
namespace Id3;

use Id3\Frame\ApicFrame;
use Id3\Std\Collection;

abstract class Id3Accessor extends GetSet {
    protected function _get($property): mixed {
        if (property_exists($this, $property) || array_key_exists($property, $this->_properties)) {
            return parent::_get($property);
        } else {
            return null;
        }
    }

    protected abstract function guessDuration(): int;

    public function getAlbum(): string {
        return trim($this->getTal() ?: $this->getTalb() ?: '');
    }

    public function getAlbumArtist(): string {
        return trim($this->getTp2() ?: $this->getTpe2() ?: '');
    }

    public function getAlbumImage(): ?ApicFrame {
        $images = $this->getPic() ?: $this->getApic() ?: null;
        if ($images instanceof Collection) {
            foreach ($images as $image) {
                if ($image->getType() == 'Cover front') {
                    return $image;
                }
            }
            return $images->current();
        } else {
            return $images;
        }
    }

    public function getArtist(): string {
        return trim($this->getTp1() ?: $this->getTpe1() ?: '');
    }

    public function getGenre(): string {
        return trim($this->getTco() ?: $this->getTcon() ?: '');
    }

    public function getDuration(): int {
        $frame = $this->getTle() ?: $this->getTlen() ?: null;
        return $frame ? intval($frame?->getValue()) : $this->guessDuration();
    }

    public function getPartOfSet(): string {
        return trim($this->getTpa() ?: $this->getTpos() ?: '');
    }

    public function getTitle(): string {
        return trim($this->getTt2() ?: $this->getTit2() ?: '');
    }

    public function getTrack(): string {
        return trim($this->getTrk() ?: $this->getTrck() ?: '');
    }

    public function getYear(): string {
        return trim($this->getTye() ?: $this->getTyer() ?: '');
    }
}
