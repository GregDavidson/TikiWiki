/**
 * undo.js
 * A javascript module to support undo/redo on a object level 
 */

module.exports = {
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
  initUndo: function(undoObject, maxHistory, undoName, redoName, changesHistory, changesIndex) {
    if(typeof changesHistory === "undefined" || changesHistory === null) {
       undoObject.__changesHistory = [];
       undoObject.__changesIndex = {idx: -1};
       changesHistory = undoObject.__changesHistory;
       changesIndex = undoObject.__changesIndex;
    }
    
    if(typeof maxHistory === "undefined" || maxHistory === null) {
      maxHistory = -1;
    }


    function handleChange(changes) {
      // console.log('Notified of change');
      changes.forEach(function(change) {
        if(undoObject[change.name] === changesHistory || undoObject[change.name] === changesIndex)
          return;

        //  Cascade changes
        // if(typeof undoObject[change.name] === 'object' && undoObject[change.name] !== null) {
        //   initUndo(undoObject[change.name], undoName, redoName, changesHistory, changesIndex);
        // }
        
        // console.log('Change recorded', change.type, change.name, change.oldValue);
        // Remove any remaining changes
        changesHistory.splice(changesIndex.idx + 1, changesHistory.length - changesIndex.idx - 1);
        changesHistory.push({ name: change.name, oldValue: change.oldValue, newValue: undoObject[change.name]});
        if(changesHistory.length > maxHistory && maxHistory > -1) {
          changesHistory.shift(); 
        }
        else {
          changesIndex.idx++;
        }
      });
    }
    
    if(typeof undoName === "undefined" || undoName === null) {
      undoName = "undo"; 
    }

    if(typeof redoName === "undefined" || redoName === null) {
      redoName = "redo"; 
    }
   
    // Create undo function 
    undoObject[undoName] = function() {
      if(changesIndex.idx === -1) {
        console.error('No more changes to undo');
        return;
      }
      Object.unobserve(undoObject, handleChange);
      
      if(typeof changesHistory[changesIndex.idx].oldValue === "undefined")
        delete undoObject[changesHistory[changesIndex.idx].name];
      else
        undoObject[changesHistory[changesIndex.idx].name] = changesHistory[changesIndex.idx].oldValue;
      changesIndex.idx = Math.max(changesIndex.idx - 1, -1);
      Object.observe(undoObject, handleChange); 
    }

    // Create redo function
    undoObject[redoName] = function() {
      if(changesIndex.idx === changesHistory.length - 1) {
        console.error('No more changes to redo');
        return;
      }
      Object.unobserve(undoObject, handleChange); 
       
      changesIndex.idx = Math.min(changesIndex.idx + 1, changesHistory.length - 1);  
      if(typeof changesHistory[changesIndex.idx].newValue === "undefined")
        delete undoObject[changesHistory[changesIndex.idx].name];
      else
        undoObject[changesHistory[changesIndex.idx].name] = changesHistory[changesIndex.idx].newValue;
      Object.observe(undoObject, handleChange);
    }
    
 
    Object.observe(undoObject, handleChange);
  
    return undoObject;
  }
}
