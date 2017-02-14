undojs
=======

A javascript module to enable undo/redo on a object level.

## Installation

  npm install undojs --save
  
## Usage

```javascript
  var undojs = require('undojs');
  
  var anyObject = new Object();
  undojs.initUndo(anyObject);
  
  // TATATA! your object now has the undo / redo abiltity
  
  anyObject.test = 1;
  anyObject.test = 3;
  anyObject.undo();
  console.log(anyObject.test)
  // -> 1
  anyObject.redo();
  console.log(anyObject.test)
  // -> 3
```
You can specify how many steps are recorded (default: infinity):
```javascript
  var undojs = require('undojs');
  
  var anyObject = new Object();
  undojs.initUndo(anyObject, 1);
  
  anyObject.test = 1;
  anyObject.test = 3;
  anyObject.undo();
  console.log(anyObject.test)
  // -> 1
  anyObject.undo();
  // -> No more changes to undo
```

You can even specify the function names (default: 'undo'/'redo'):
```javascript
  var undojs = require('undojs');
  
  var anyObject = new Object();
  undojs.initUndo(anyObject, -1, 'un', 're');
  
  anyObject.test = 1;
  anyObject.test = 3;
  anyObject.un();
  console.log(anyObject.test)
  // -> 1
  anyObject.re();
  console.log(anyObject.test)
  // -> 3
```

## API
```javascript
  /**
  * Initalise the undo/redo for an object
  *
  * @param {Object} undoObject
  * @param {Number} maxHistory - the maximum history length
  * @param {String} undoName - name of the undo function by default 'undo'
  * @param {String} redoName - name of the redo function by default 'redo'
  * @param {Array}  changesHistory - array to hold changes
  * @param {Number} changesIndex - current index in the changes history
  * @return {Object} object with undo redo mechanism
  */
  initUndo: function(undoObject, maxHistory, undoName, redoName, changesHistory, changesIndex) 
```

## Tests

```
  npm test
```

## Contributing

Contribution always appreciated :)

## Release History

* 0.0.1 Initial release
