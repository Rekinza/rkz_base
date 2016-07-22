<?php
/**
 * MageTitan's Skip Shipping Method
 * For assistance please contact devsupport@magetitan.com
 *
 * Copyright (c) 2011 NetMediaGroup
 * All rights reserved.
 *
 * Redistribution with or without modification, without permission,
 * is strictly prohibited.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    MageTitan
 * @copyright   Copyright (c) 2011 NetMediaGroup  (www.netmediagroup.com)
 * @license     http://www.magetitan.com/licenses/restricted.html
 * @package     MageTitan_SkipShippingMethod
 */

class MageTitan_SkipShippingMethod_Model_Adminhtml_System_Method
{
  const FIRST_AVAILABLE = 'first_available';
  const LOWEST_COST     = 'lowest_cost';
  const HIGHEST_COST    = 'highest_cost';

  public function toOptionArray()
  {
    return array(
      array(
      'value' => self::FIRST_AVAILABLE,
      'label' => Mage::helper('skipshippingmethod')->__('First Available Shipping Method'),
      ),
      array(
      'value' => self::LOWEST_COST,
      'label' => Mage::helper('skipshippingmethod')->__('Lowest Shipping Cost'),
      ),
      array(
      'value' => self::HIGHEST_COST,
      'label' => Mage::helper('skipshippingmethod')->__('Highest Shipping Cost'),
      ));
  }

}