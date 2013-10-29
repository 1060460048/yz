<?php
/**
 * YzCrudGenerator class file.
 * @author Pavel Agalecky <pavel.agalecky@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 */

Yz::get()->registerBootstrap();

Yii::import('bootstrap.gii.bootstrap.BootstrapGenerator');

class YzCrudGenerator extends BootstrapGenerator
{
	public $codeModel = 'yz.gii.generators.YzCrud.YzCrudCode';
}