import assert from 'node:assert/strict';
import { setupDeleteConfirmation } from '../../resources/js/delete-confirmation.js';

let submit;
const message = { textContent: '' };
class Dialog extends EventTarget {
    ownerDocument = { addEventListener: (_, handler) => { submit = handler; } };
    open = false;
    querySelector() { return message; }
    showModal() { this.open = true; }
    close(value = 'cancel') {
        this.returnValue = value;
        this.open = false;
        this.dispatchEvent(new Event('close'));
    }
}
const dialog = new Dialog();
const confirmDeletion = setupDeleteConfirmation(dialog);
function form(method = 'DELETE', removed = 0, alreadyConfirmed = false) {
    return {
        dataset: { deleteConfirmation: "Hapus guru O'Connor?" },
        submissions: 0,
        hasAttribute: () => alreadyConfirmed,
        querySelector: () => ({ value: method }),
        querySelectorAll: () => Array(removed),
        requestSubmit(button) {
            this.submissions++;
            this.button = button;
            const event = submission(this);
            submit(event);
            assert.equal(event.defaultPrevented, false, 'Approved submission must not reopen confirmation');
        },
    };
}
function submission(target, submitter = null) {
    return { target, submitter, defaultPrevented: false, preventDefault() { this.defaultPrevented = true; } };
}

const deletion = form();
let event = submission(deletion);
let pending = submit(event);
assert.equal(event.defaultPrevented, true);
assert.equal(dialog.open, true);
assert.equal(message.textContent, "Hapus guru O'Connor?");
assert.equal(await confirmDeletion('Second click'), false, 'One dialog at a time');
dialog.close(); // Cancel button or Escape must leave the data untouched.
await pending;
assert.equal(deletion.submissions, 0);

const button = {};
pending = submit(submission(deletion, button));
dialog.close('confirm');
await pending;
assert.equal(deletion.submissions, 1);
assert.equal(deletion.button, button);
assert.equal(dialog.open, false);

const update = form('PATCH');
event = submission(update);
await submit(event);
assert.equal(event.defaultPrevented, false);
assert.equal(dialog.open, false);
const removal = form('PATCH', 2);
event = submission(removal);
pending = submit(event);
assert.equal(event.defaultPrevented, true);
assert.match(message.textContent, /Hapus 2 guru/);
dialog.close('confirm');
await pending;
assert.equal(removal.submissions, 1);

const account = submission(form('DELETE', 0, true));
await submit(account);
assert.equal(account.defaultPrevented, false, 'Account password dialog already confirms deletion');
const row = confirmDeletion('Hapus tunjangan?');
dialog.close();
assert.equal(await row, false);
console.log('Delete confirmation checks passed.');
