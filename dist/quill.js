function formatText(action, textareaId) {
  const textarea = document.getElementById(textareaId);
  if (!textarea) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const selectedText = textarea.value.substring(start, end);

  let formattedText = "";
  if (action === "bold") {
    formattedText = `<strong>${selectedText}</strong>`;
  } else if (action === "italic") {
    formattedText = `<em>${selectedText}</em>`;
  } else if (action === "underline") {
    formattedText = `<u>${selectedText}</u>`;
  } else if (action === "paragraph") {
    formattedText = `<p>${selectedText}</p>`;
  }

  textarea.value =
    textarea.value.substring(0, start) +
    formattedText +
    textarea.value.substring(end);

  textarea.selectionStart = textarea.selectionEnd =
    start + formattedText.length;
}
