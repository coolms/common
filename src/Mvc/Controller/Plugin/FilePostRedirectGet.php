<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Controller\Plugin;

use Zend\InputFilter\CollectionInputFilter,
    Zend\InputFilter\InputFilterInterface,
    Zend\Mvc\Controller\Plugin\FilePostRedirectGet as ZendFilePostRedirectGet;

/**
 * Plugin to help facilitate Post/Redirect/Get for file upload forms
 * (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * Requires that the Form's File inputs contain a 'fileRenameUpload' filter
 * with the target option set: 'target' => /valid/target/path'.
 * This is so the files are moved to a new location between requests.
 * If this filter is not added, the temporary upload files will disappear
 * between requests.
 */
class FilePostRedirectGet extends ZendFilePostRedirectGet
{
    /**
     * {@inheritDoc}
     */
    protected function traverseInputs(InputFilterInterface $inputFilter, $values, $callback)
    {
        $returnValues = null;
        foreach ($values as $name => $value) {
            if (!$inputFilter->has($name)) {
                continue;
            }

            $input = $inputFilter->get($name);
            if ($input instanceof InputFilterInterface && is_array($value)) {
                if ($input instanceof CollectionInputFilter) {
                    foreach ($value as $key => $subValue) {
                        $returnValues[$name][$key] = $this->traverseInputs(
                            $input->getInputFilter(),
                            $subValue,
                            $callback
                        );
                    }

                    continue;
                }

                $retVal = $this->traverseInputs($input, $value, $callback);
                if (null !== $retVal) {
                    $returnValues[$name] = $retVal;
                }
                continue;
            }

            $retVal = $callback($input, $value);
            if (null !== $retVal) {
                $returnValues[$name] = $retVal;
            }
        }

        return $returnValues;
    }
}
