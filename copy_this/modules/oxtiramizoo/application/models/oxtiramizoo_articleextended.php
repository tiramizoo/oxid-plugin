<?php
/**
 * This file is part of the oxTiramizoo OXID eShop plugin.
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  module
 * @package   oxTiramizoo
 * @author    Tiramizoo GmbH <support@tiramizoo.com>
 * @copyright Tiramizoo GmbH
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Tiramizoo Article Extended manager.
 *
 * @extends oxBase
 * @package oxTiramizoo
 */
class oxTiramizoo_ArticleExtended extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooarticleextended';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_ArticleExtended';

    /**
     * Article object
     *
     * @var oxArticle
     */
    protected $_oArticle = null;


    /**
     * Effective data array
     *
     * @var mixed
     */
    protected $_aEffectiveData = null;

    /**
     * Class constructor
     *
     * @extend oxBase::__construct()
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooarticleextended' );
    }

    /**
     * Returns
     *
     * @param  string $sArticleId article oxid
     *
     * @return string oxid
     */
    public function getIdByArticleId($sArticleId)
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXARTICLEID = '" . $sArticleId . "';";
        return $oDb->getOne($sQ);
    }

    /**
     * Loads object with article and set the article
     *
     * @param  oxArticle $oArticle
     *
     * @return void
     */
    public function loadByArticle($oArticle)
    {
        $this->_oArticle = $oArticle;

        $soxId = $this->getIdByArticleId($oArticle->getId());

        if ($soxId) {
            $this->load($soxId);
        }
    }

    /**
     * Returns article
     *
     * @return oxArticle
     */
    public function getArticle()
    {
        return $this->_oArticle;
    }

    /**
     * Check if article is enabled. Inherit enable property from category and global settings.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if (!$this->getEffectiveDataValue('tiramizoo_enable')) {
            return false;
        }

        if (!$this->getEffectiveDataValue('weight') || !$this->getEffectiveDataValue('width') || !$this->getEffectiveDataValue('height') || !$this->getEffectiveDataValue('length')) {
            return false;
        }

        return true;
    }

    /**
     * Returns information about individual packaging
     *
     * @return boolean
     */
    public function hasIndividualPackage()
    {
        if ($this->getEffectiveDataValue('tiramizoo_use_package')) {
            return false;
        }

        return true;
    }

    /**
     * Check if article has own dimensions
     *
     * @return boolean
     */
    public function hasWeightAndDimensions()
    {
        $oArticle = $this->getArticle();
        return $oArticle->oxarticles__oxweight->value &&
            $oArticle->oxarticles__oxwidth->value &&
            $oArticle->oxarticles__oxheight->value &&
            $oArticle->oxarticles__oxlength->value;
    }

    /**
     * Getting effective data for product and assign value to object property lazy loading.
     *
     * @param  boolean $bForce if true force build
     * @return array          contains effective value for product's dimensions and weight, enable, individual packaging
     */
    public function buildEffectiveData($bForce = false)
    {
        if (!is_array($this->_aEffectiveData) || $bForce) {
            $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');
            $this->_aEffectiveData = $oArticleInheritedData->getArticleEffectiveData($this->getArticle());
        }

        return $this->_aEffectiveData;
    }

    /**
     * Getter method for _aEffectiveData property. Call buildEffectiveData method.
     *
     * @return array
     */
    public function getEffectiveData()
    {
        $this->buildEffectiveData();

        return $this->_aEffectiveData;
    }

    /**
     * Returns a value for specified key. Call buildEffectiveData method.
     *
     * @param  string $sProperty
     * @return mixed
     */
    public function getEffectiveDataValue($sProperty)
    {
        $this->buildEffectiveData();

        return $this->_aEffectiveData[$sProperty];
    }
}
