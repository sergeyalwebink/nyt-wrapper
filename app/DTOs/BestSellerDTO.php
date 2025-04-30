<?php

namespace App\DTOs;

class BestSellerDTO
{
    public string $title;
    public string $author;
    public string $description;
    public string $contributor;
    public string $price;
    public string $ageGroup;
    public string $publisher;
    public array $isbns;
    public array $ranksHistory;
    public array $reviews;

    /**
     * Factory method to create a new instance of the DTO from an array.
     *
     * @param array $data
     * @return BestSellerDTO
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();

        $dto->title = $data['title'] ?? '';
        $dto->author = $data['author'] ?? '';
        $dto->description = $data['description'] ?? '';
        $dto->contributor = $data['contributor'] ?? '';
        $dto->price = $data['price'] ?? '';
        $dto->ageGroup = $data['age_group'] ?? '';
        $dto->publisher = $data['publisher'] ?? '';
        $dto->isbns = $data['isbns'] ?? [];
        $dto->ranksHistory = $data['ranks_history'] ?? [];
        $dto->reviews = $data['reviews'] ?? [];

        return $dto;
    }

    /**
     * Convert the DTO to an array representation.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'contributor' => $this->contributor,
            'price' => $this->price,
            'age_group' => $this->ageGroup,
            'publisher' => $this->publisher,
            'isbns' => $this->isbns,
            'ranks_history' => $this->ranksHistory,
            'reviews' => $this->reviews,
        ];
    }
}
