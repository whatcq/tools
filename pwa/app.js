let db;

function initDB() {
    const request = indexedDB.open("notepadDB", 1);

    request.onupgradeneeded = (event) => {
        db = event.target.result;
        const notesStore = db.createObjectStore("notes", { keyPath: "id", autoIncrement: true });
        notesStore.createIndex("time", "time");
        notesStore.createIndex("tags", "tags");
        notesStore.createIndex("location", "location");
    };

    request.onsuccess = (event) => {
        db = event.target.result;
        displayNotes();
    };

    request.onerror = (event) => {
        console.error("Database error: " + event.target.errorCode);
    };
}

function saveNote() {
    const time = document.getElementById("noteTime").value;
    const content = document.getElementById("noteContent").value;
    const tags = document.getElementById("noteTags").value;
    const location = document.getElementById("noteLocation").value;

    const transaction = db.transaction(["notes"], "readwrite");
    const notesStore = transaction.objectStore("notes");

    notesStore.add({ time, content, tags, location });

    transaction.onsuccess = () => {
        displayNotes();
    };

    transaction.onerror = (event) => {
        console.error("Transaction error: " + event.target.errorCode);
    };

    document.getElementById("noteForm").reset();
}

function displayNotes() {
    const noteList = document.getElementById("noteList");
    noteList.innerHTML = "";

    const transaction = db.transaction(["notes"], "readonly");
    const notesStore = transaction.objectStore("notes");

    notesStore.getAll().onsuccess = (event) => {
        const notes = event.target.result;
        notes.forEach(note => {
            const li = document.createElement("li");
            li.innerHTML = `<strong>${note.time}</strong><br>${note.content}<br><em>${note.tags} | ${note.location}</em>`;
            noteList.appendChild(li);
        });
    };
}

document.getElementById("noteForm").addEventListener("submit", (event) => {
    event.preventDefault();
    saveNote();
});

window.onload = initDB;
