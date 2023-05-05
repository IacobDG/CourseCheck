function encryptPassword(password, salt) 
{
  if (!password || !salt) 
  {
    throw new Error("Both password and salt are required.");
  }

  const shaObj = new jsSHA("SHA-256", "TEXT");
  shaObj.update(password + salt);
  const newPassword = shaObj.getHash("HEX");
  return newPassword;
}


/*/ Generate a random encryption key and IV
let encryptionKey = window.crypto.subtle.generateKey(
    {
      name: 'AES-GCM',
      length: 256
    },
    true,
    ['encrypt', 'decrypt']
  );
  
  let iv = window.crypto.getRandomValues(new Uint8Array(12));
  
  // Convert the password to a Uint8Array
  let password = new TextEncoder().encode(document.getElementById('password').value);
  
  // Encrypt the password with AES-GCM
  window.crypto.subtle.encrypt(
    {
      name: 'AES-GCM',
      iv: iv,
      tagLength: 128
    },
    encryptionKey,
    password
  ).then(encryptedPassword => {
    // Convert the encrypted password and IV to base64 for transmission to the server
    let base64Password = btoa(String.fromCharCode(...new Uint8Array(encryptedPassword)));
    let base64Iv = btoa(String.fromCharCode(...iv));
  
    // Set the encrypted password and IV as hidden fields in a form
    document.getElementById('encrypted_password').value = base64Password;
    document.getElementById('iv').value = base64Iv;
  
    // Submit the form to the server
    document.getElementById('my_form').submit();
  });*/
  