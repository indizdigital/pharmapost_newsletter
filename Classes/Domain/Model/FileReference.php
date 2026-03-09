<?php
namespace Phi\PhiNewsletter\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * FileReference
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {
    /**
     * @var string
     */
    protected $fieldname = 'image';

    /**
     * @var \Vendor\Myextension\Domain\Model\File
     */
    protected $file;

    /**
     * @var \string tablenames
     */
    protected $tablenames;


    /**
     * @var \string table_local
     */
    protected $table_local;

    /**
     * Set table_local
     *
     * @param \string $table_local
     */
    public function setTableLocal($table_local) {
        $this->table_local = $table_local;
    }
    /**
     * Get table_local
     *
     * @return \string $table_local
     */
    public function getTableLocal() {
        return $this->table_local;
    }
    /**
     * Set tablenames
     *
     * @param \string $tablenames
     */
    public function setTablenames($tablenames) {
        $this->tablenames = $tablenames;
    }
    /**
     * Get tablenames
     *
     * @return \string $tablenames
     */
    public function getTablenames($tablenames) {
        return $this->tablenames;
    }
    /**
     * Set file
     *
     * @param \Vendor\Myextension\Domain\Model\File $file
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return \Vendor\Myextension\Domain\Model\File
     */
    public function getFile() {
        return $this->file;
    }
    /**
     * Set fieldname
     *
     * @param \string $fieldname
     */
    public function setFieldname($fieldname) {
        $this->fieldname = $fieldname;
    }

    /**
     * Get fieldname
     *
     * @return \string
     */
    public function getFieldname() {
        return $this->fieldname;
    }
}
