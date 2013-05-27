<?php



class Unit_Translations_TranslationsTest extends OxidTestCase
{
	public function testTranslationEn()
	{
        $this->assertEquals('Today', oxRegistry::getLang()->translateString('oxTiramizoo_Today', 1, false));
        $this->assertEquals('Settings', oxRegistry::getLang()->translateString('oxTiramizoo_settings', 1, true));
	}

    public function testTranslationDe()
    {
        $this->assertEquals('Heute', oxRegistry::getLang()->translateString('oxTiramizoo_Today', 0, false));
        $this->assertEquals('Einstellungen', oxRegistry::getLang()->translateString('oxTiramizoo_settings', 0, true));
    }

}
