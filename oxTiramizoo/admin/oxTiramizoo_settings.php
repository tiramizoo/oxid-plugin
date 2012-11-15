<?php

class oxTiramizoo_settings extends Shop_Config
{
  const OX_TIRAMIZOO_MODULE_NAME = 'oxTiramizoo';
  
  /**
   * Current Version String.
   * @var string
   */
  protected $_sVersion = 'oxTiramizoo Module v0.1';
  /**
   * Current class template.
   * @var string
   */
  protected $_sThisTemplate = 'oxTiramizoo_settings.tpl';
  
  protected $oxTiramizoo_is_module_installed = null;

  function Init() {
    
    return parent::Init();
  }

  /**
   * Executes parent method parent::render() and returns name of template
   * file "payengine.tpl".
   *
   * @return string
   */
  public function render()
  {
    $myConfig  = $this->getConfig();
    parent::render();

    $sCurrentAdminShop = $myConfig->getShopId();
    return $this->_sThisTemplate;
  }
  
  /**
   * Saves shop configuration variables
   *
   * @return null
   */
  public function saveConfVars()
  {
    $myConfig = $this->getConfig();

    $aConfBools = oxConfig::getParameter( "confbools" );
    $aConfStrs  = oxConfig::getParameter( "confstrs" );
    $aConfArrs  = oxConfig::getParameter( "confarrs" );
    $aConfAarrs = oxConfig::getParameter( "confaarrs" );

        // special case for min order price value
    if ( $aConfStrs['iMinOrderPrice'] ) {
      $aConfStrs['iMinOrderPrice'] = str_replace( ',', '.', $aConfStrs['iMinOrderPrice'] );
    }

    if ( is_array( $aConfBools ) ) {
      foreach ( $aConfBools as $sVarName => $sVarVal ) {
          $myConfig->saveShopConfVar( "bool", $sVarName, $sVarVal);
      }
    }

    if ( is_array( $aConfStrs ) ) {
      foreach ( $aConfStrs as $sVarName => $sVarVal ) {
        $myConfig->saveShopConfVar( "str", $sVarName, $sVarVal);
      }
    }

    if ( is_array( $aConfArrs ) ) {
      foreach ( $aConfArrs as $sVarName => $aVarVal ) {
        // home country multiple selectlist feature
        if ( !is_array( $aVarVal ) ) {
          $aVarVal = $this->_multilineToArray($aVarVal);
        }
        $myConfig->saveShopConfVar("arr", $sVarName, $aVarVal);
      }
    }

    if ( is_array( $aConfAarrs ) ) {
      foreach ( $aConfAarrs as $sVarName => $aVarVal ) {
        $myConfig->saveShopConfVar( "aarr", $sVarName, $this->_multilineToAarray( $aVarVal ));
      }
    }
  }
  


  /**
   * Saves main user parameters.
   *
   * @return mixed
   */
  public function save()
  {
    // saving config params
    $this->saveConfVars();

        
    // clear cache 
    oxUtils::getInstance()->rebuildCache();
    
    
        return;
    }
  

}