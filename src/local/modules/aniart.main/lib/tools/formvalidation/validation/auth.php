<?
namespace Aniart\Main\Tools\FormValidation\Validation;

use Aniart\Main\Tools\FormValidation\AbstractValidation;

class Auth extends AbstractValidation
{
    protected $requiredFields = ['LOGIN', 'PASSWORD'];

    public function validate()
    {
        return $this->getRequired($this->arRequiredLogin, 'auth');
    }
}

