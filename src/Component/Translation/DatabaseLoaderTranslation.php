<?php
namespace App\Component\Translation;

use App\Repository\TranslationRepository;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class DatabaseLoaderTranslation implements LoaderInterface
{
    public function __construct(
        private TranslationRepository $translation
    )
    {
        $this->translation = $translation;
        
    }

    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        
    }
}