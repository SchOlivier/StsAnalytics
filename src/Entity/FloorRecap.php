<?php

namespace App\Entity;

use App\Entity\ref\enum\EnumPath;
use App\Entity\ref\item\Card;
use App\Entity\ref\item\Item;
use App\Entity\room\IRoom;

class FloorRecap
{
    private int $floor;
    private int $currentGold;
    private int $currentHP;
    private int $maxHP;
    private EnumPath $path; // on the map ?
    private array $purchases = [];
    private array $upgrades = [];
    private array $purges = [];
    private array $rooms = []; // Reality ? + F
    private array $rewards = [];

    public function __construct()
    {
    }

    public function __toString()
    {
        $string = "floor " . $this->floor . " : " . $this->path->name . "\n";
        $string .= "current Gold : " . $this->currentGold . "\n";
        $string .= "current HP : " . $this->currentHP . "\n";
        $string .= "max HP : " . $this->maxHP . "\n";
        $string .= "Upgrades : \n";
        foreach ($this->upgrades as $upgrade) {
            $string .= "\t$upgrade\n";
        }
        $string .= "Purges : \n";
        foreach ($this->purges as $purges) {
            $string .= "\t$purges\n";
        }
        foreach ($this->rooms as $room) {
            $string .= $room->getRoomRecap() . "\n";
        }
        $string .= "Rewards : \n";
        foreach ($this->rewards as $reward) {
            $string .= "\t$reward\n";
        }
        $string .= "à être continué...\n";
        return $string;
    }

    /**
     * Get the value of floor
     *
     * @return int
     */
    public function getFloor(): int
    {
        return $this->floor;
    }

    /**
     * Set the value of floor
     *
     * @param int $floor
     *
     * @return self
     */
    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get the value of currentGold
     *
     * @return int
     */
    public function getCurrentGold(): int
    {
        return $this->currentGold;
    }

    /**
     * Set the value of currentGold
     *
     * @param int $currentGold
     *
     * @return self
     */
    public function setCurrentGold(int $currentGold): self
    {
        $this->currentGold = $currentGold;

        return $this;
    }

    /**
     * Get the value of currentHP
     *
     * @return int
     */
    public function getCurrentHP(): int
    {
        return $this->currentHP;
    }

    /**
     * Set the value of currentHP
     *
     * @param int $currentHP
     *
     * @return self
     */
    public function setCurrentHP(int $currentHP): self
    {
        $this->currentHP = $currentHP;

        return $this;
    }

    /**
     * Get the value of maxHP
     *
     * @return int
     */
    public function getMaxHP(): int
    {
        return $this->maxHP;
    }

    /**
     * Set the value of maxHP
     *
     * @param int $maxHP
     *
     * @return self
     */
    public function setMaxHP(int $maxHP): self
    {
        $this->maxHP = $maxHP;

        return $this;
    }

    /**
     * Get the value of path
     *
     * @return EnumPath
     */
    public function getPath(): EnumPath
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @param EnumPath $path
     *
     * @return self
     */
    public function setPath(EnumPath $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of purchases
     *
     * @return array
     */
    public function getPurchases(): array
    {
        return $this->purchases;
    }

    /**
     * Set the value of purchases
     *
     * @param array $purchases
     *
     * @return self
     */
    public function setPurchases(array $purchases): self
    {
        $this->purchases = $purchases;

        return $this;
    }

    public function addPurchase(Item $purchase)
    {
        $this->purchases[] = $purchase;
    }

    /**
     * Get the value of purges
     *
     * @return array
     */
    public function getPurges(): array
    {
        return $this->purges;
    }

    /**
     * Set the value of purges
     *
     * @param array $purges
     *
     * @return self
     */
    public function setPurges(array $purges): self
    {
        $this->purges = $purges;

        return $this;
    }

    /**
     * Get the value of upgrades
     *
     * @return array
     */
    public function getUpgrades(): array
    {
        return $this->upgrades;
    }

    /**
     * Get the value of rooms
     *
     * @return array
     */
    public function getRooms(): array
    {
        return $this->rooms;
    }

    /**
     * Get the value of rewards
     *
     * @return array
     */
    public function getRewards(): array
    {
        return $this->rewards;
    }

    // /**
    //  * Get the value of potionUse
    //  *
    //  * @return array
    //  */
    // public function getPotionUse(): array
    // {
    //     return $this->potionUse;
    // }

    public function addRoom(IRoom $room)
    {
        $this->rooms[] = $room;
    }

    public function addUpgrade(Card $card)
    {
        $this->upgrades[] = $card;
    }

    public function addPurge(Card $card)
    {
        $this->purges[] = $card;
    }

    public function addReward(Reward $reward)
    {
        $this->rewards[] = $reward;
    }
}
