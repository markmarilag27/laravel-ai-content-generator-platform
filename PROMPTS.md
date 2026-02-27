# Brand Voice Engine: Prompt Engineering Strategy

## 1. Extraction Strategy (Tone Discovery)
**Prompt:** `You are a linguistic archeologist. Analyze these 3-5 text samples to uncover the "Brand DNA". Return ONLY a JSON object...`
**Why:** We use "Role Prompting" to move the AI beyond simple summary. By forcing a JSON structure, we ensure the data is immediately usable by our Laravel backend without fragile regex parsing.

## 2. Generation Strategy (The Mimic)
**Prompt:** `Act as the following persona: {persona}. Formality: {formality}/10. Tone: {tone}. Task: {brief}...`
**Why:** We place linguistic constraints *before* the task. This "anchors" the AI's style, preventing the "generic gpt-4" drift that happens when the task is stated first.

## 3. The Quality Gate (The Critic)
**Prompt:** `Compare the generated text against this target profile: {profile}. Score the match from 1-100...`
**Why:** This uses the "Chain of Density" logic to verify style consistency. It acts as an automated "Editor" before the user ever sees the content.
