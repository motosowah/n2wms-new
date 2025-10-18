<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <title><?= $this->renderSection('title') ?></title>

  <!-- Materialize CSS -->
  <link rel="stylesheet" href="/assets/css/materialize.min.css">
  <!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
  <link rel="stylesheet" href="/assets/css/material-icons.css">

<style>
body{
  max-width: 500px;
}
</style>
</head>
<body>
  <nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container">
      <a id="logo-container" class="brand-logo center"><?= esc($nav) ?></a>

      <ul id="nav-mobile" class="sidenav">
        <li><a href="/mobile"><i class="material-icons">account_circle</i>WMS New - <?= esc($username) ?></a></li>
        <hr/>
        <li><a href="/mobile"><i class="material-icons">home</i>Home</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">shopping_cart</i>Palletizing</a></li>
        <li><a href="/mobile/staging"><i class="material-icons">grid_on</i>Staging</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">grid_on</i>Moving</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">grid_on</i>Splitting</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">grid_on</i>Delivery</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">grid_on</i>Outbound</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">grid_on</i>Loading</a></li>
        <li><a href="/mobile/palletizing"><i class="material-icons">grid_on</i>Stock</a></li>
      </ul>

      <a data-target="nav-mobile" class="sidenav-trigger show-on-large left" style="cursor: pointer;">
        <i class="material-icons">menu</i>
      </a>
    </div>
  </nav>

  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
    <!-- MAIN PAGE CONTENT START -->
      <?= $this->renderSection('content') ?>
    <!-- MAIN PAGE CONTENT END -->
    </div>
  </div>



  <!-- modal error message -->
  <div id="modal_error" class="modal">
    <div class="modal-content red-text text-darken-4">
      <h4><i class="material-icons left">error</i>Error!</h4>
      <hr/>
      <p id="modal_error_message"></p>
    </div>
    <div class="modal-footer">
      <a class="modal-close waves-effect waves-red btn-flat">OK</a>
    </div>
  </div> 


  
<!-- Materialize JS -->
<script src="/assets/js/materialize.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // init sidebar //
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems);
  });



// - reusable cookies CRUD function - //
/**
 * Cookie Utility Functions
 * Comprehensive CRUD operations for cookies with various options
 */

class CookieUtils {
  /**
   * Set a cookie with various options
   * @param {string} name - Cookie name
   * @param {string} value - Cookie value
   * @param {Object} options - Cookie options
   * @param {number} options.days - Days until expiration
   * @param {number} options.hours - Hours until expiration
   * @param {number} options.minutes - Minutes until expiration
   * @param {string} options.path - Cookie path
   * @param {string} options.domain - Cookie domain
   * @param {boolean} options.secure - Secure flag
   * @param {string} options.sameSite - SameSite attribute ('Strict', 'Lax', 'None')
   */
  static set(name, value, options = {}) {
    let cookieString = `${encodeURIComponent(name)}=${encodeURIComponent(value)}`;

    // Handle expiration
    if (options.days || options.hours || options.minutes) {
      const expires = new Date();
      if (options.days) expires.setDate(expires.getDate() + options.days);
      if (options.hours) expires.setHours(expires.getHours() + options.hours);
      if (options.minutes) expires.setMinutes(expires.getMinutes() + options.minutes);
      cookieString += `; expires=${expires.toUTCString()}`;
    }

    // Add other options
    if (options.path) cookieString += `; path=${options.path}`;
    if (options.domain) cookieString += `; domain=${options.domain}`;
    if (options.secure) cookieString += '; secure';
    if (options.sameSite) cookieString += `; samesite=${options.sameSite}`;

    document.cookie = cookieString;
  }

  /**
   * Get a cookie value by name
   * @param {string} name - Cookie name to retrieve
   * @returns {string|null} - Cookie value or null if not found
   */
  static get(name) {
    const nameEQ = `${encodeURIComponent(name)}=`;
    const cookies = document.cookie.split(';');
    
    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i].trim();
      if (cookie.indexOf(nameEQ) === 0) {
        return decodeURIComponent(cookie.substring(nameEQ.length));
      }
    }
    return null;
  }

  /**
   * Get all cookies as an object
   * @returns {Object} - Object containing all cookies
   */
  static getAll() {
    const cookies = {};
    const cookieArray = document.cookie.split(';');
    
    cookieArray.forEach(cookie => {
      const [name, value] = cookie.trim().split('=');
      if (name && value) {
        cookies[decodeURIComponent(name)] = decodeURIComponent(value);
      }
    });
    
    return cookies;
  }

  /**
   * Check if a cookie exists
   * @param {string} name - Cookie name to check
   * @returns {boolean} - True if cookie exists
   */
  static has(name) {
    return this.get(name) !== null;
  }

  /**
   * Remove a cookie
   * @param {string} name - Cookie name to remove
   * @param {Object} options - Additional options for path and domain
   */
  static remove(name, options = {}) {
    this.set(name, '', {
      days: -1, // Set to past date to expire immediately
      path: options.path,
      domain: options.domain
    });
  }

  /**
   * Clear all cookies (for current path and domain)
   */
  static clearAll() {
    const cookies = this.getAll();
    Object.keys(cookies).forEach(cookieName => {
      this.remove(cookieName);
    });
  }

  /**
   * Set a cookie with JSON data
   * @param {string} name - Cookie name
   * @param {Object} data - JSON data to store
   * @param {Object} options - Cookie options
   */
  static setJSON(name, data, options = {}) {
    const jsonString = JSON.stringify(data);
    this.set(name, jsonString, options);
  }

  /**
   * Get a cookie and parse as JSON
   * @param {string} name - Cookie name
   * @returns {Object|null} - Parsed JSON object or null
   */
  static getJSON(name) {
    const value = this.get(name);
    if (!value) return null;
    
    try {
      return JSON.parse(value);
    } catch (e) {
      console.warn(`Failed to parse cookie "${name}" as JSON:`, e);
      return null;
    }
  }
}

// Alternative: Function-based implementation (if you prefer functions over class)
const cookieManager = {
  set: CookieUtils.set,
  get: CookieUtils.get,
  getAll: CookieUtils.getAll,
  has: CookieUtils.has,
  remove: CookieUtils.remove,
  clearAll: CookieUtils.clearAll,
  setJSON: CookieUtils.setJSON,
  getJSON: CookieUtils.getJSON
};

</script>
</body>
</html>
