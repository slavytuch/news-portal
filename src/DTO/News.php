<?php

namespace NewsPortal\DTO;

class News
{
    public int $ID;
    public string $Name;
    public string $Code;
    public ?string $PreviewText = '';
    public ?string $DetailText = '';
    public ?string $PreviewPicture = '';
    public ?string $DetailPicture = '';
}