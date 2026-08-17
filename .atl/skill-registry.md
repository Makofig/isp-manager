# Project Skill Registry — sistema_laravel

> Auto-generated. Use for SDD context injection into sub-agent prompts.
> Detect: 2026-08-16

## User Skills

### branch-pr
**Trigger**: creating, opening, or preparing PRs for review.
**Path**: C:\Users\Markos\.config\opencode\skills\branch-pr\SKILL.md
**Compact Rules**:
- Every PR MUST link an approved issue with `Closes/Fixes/Resolves #N` — blank PRs blocked by CI
- Exactly one `type:*` label per PR
- Branch naming: `type/description` matching `^(feat|fix|chore|docs|style|refactor|perf|test|build|ci|revert)\/[a-z0-9._-]+$`
- Conventional commits: `type(scope): description` with `!` for breaking changes
- PR body requires: Linked Issue, PR Type checkbox, Summary, Changes Table, Test Plan, Contributor Checklist
- No `Co-Authored-By` trailers
- Automated checks verify issue reference, issue approval status, type label presence, and shellcheck

### chained-pr
**Trigger**: PRs over 400 lines, stacked PRs, review slices. Split oversized changes into chained PRs that protect review focus.
**Path**: C:\Users\Markos\.config\opencode\skills\chained-pr\SKILL.md
**Compact Rules**:
- Split PRs over 400 changed lines unless maintainer explicitly grants `size:exception`
- Target ~60 minutes review time per PR
- One deliverable work unit per PR; keep tests/docs with the unit they verify
- State start, end, prior dependencies, follow-up work, and out-of-scope items in every chained PR
- Every child PR must include a dependency diagram marking the current PR with `📍`
- Feature Branch Chain: create draft/no-merge tracker PR; child #1 targets tracker, later children target immediate parent
- Do not mix chain strategies after the user chooses one
- Treat polluted diffs as base bugs: retarget or rebase until only the current work unit appears

### cognitive-doc-design
**Trigger**: writing guides, READMEs, RFCs, onboarding, architecture, or review-facing docs.
**Path**: C:\Users\Markos\.config\opencode\skills\cognitive-doc-design\SKILL.md
**Compact Rules**:
- Lead with the answer (decision/action/outcome) — context comes after
- Progressive disclosure: happy path first, then details, edge cases, references
- Chunk related info into small sections; use headings, labels, callouts for signposting
- Prefer tables, checklists, examples, templates over prose that must be remembered
- Design docs so reviewers can verify intent without reconstructing the whole story
- Default doc structure: outcome-oriented title → Quick path → Details table → Checklist → Next step
- For PR docs: state what to review first, what is out of scope, link chained PRs

### comment-writer
**Trigger**: PR feedback, issue replies, reviews, Slack messages, or GitHub comments.
**Path**: C:\Users\Markos\.config\opencode\skills\comment-writer\SKILL.md
**Compact Rules**:
- Start with the actionable point — do not recap the whole PR before feedback
- Be warm and direct — sound like a thoughtful teammate, not a corporate bot
- Keep it short: 1-3 short paragraphs or a tight bullet list
- Explain WHY when asking for a change — give the technical reason
- Comment on the highest-value issue, not every tiny preference (avoid pile-ons)
- Match thread language — Spanish uses Rioplatense/voseo: `podés`, `tenés`, `fijate`, `dale`
- No em dashes — use commas, periods, or parentheses instead
- Formula: direct observation/request → why it matters (if needed) → concrete next action

### work-unit-commits
**Trigger**: implementation, commit splitting, chained PRs, or keeping tests and docs with code.
**Path**: C:\Users\Markos\.config\opencode\skills\work-unit-commits\SKILL.md
**Compact Rules**:
- A commit represents one deliverable behavior, fix, migration, or docs unit
- Do NOT commit by file type (e.g. `add models`, then `add services`)
- Keep tests in the same commit as the behavior they verify
- Keep docs with the user-visible change they explain
- A reviewer should understand why each commit exists from its diff and message alone
- Each commit should be a candidate chained PR when the change grows
- If SDD forecasts >400-line change, group commits into chained PR slices before implementation
- Commit message explains the outcome, not the file list

### issue-creation
**Trigger**: creating GitHub issues, bug reports, or feature requests.
**Path**: C:\Users\Markos\.config\opencode\skills\issue-creation\SKILL.md
**Compact Rules**:
- Blank issues disabled — MUST use a template (bug report or feature request)
- Every issue gets `status:needs-review` automatically on creation
- Maintainer MUST add `status:approved` before any PR can be opened
- Questions go to Discussions, NOT issues
- Search for duplicates before creating
- Bug Report requires: description, steps to reproduce, expected/actual behavior, OS, agent/client, shell
- Feature Request requires: problem description, proposed solution, affected area

### judgment-day
**Trigger**: judgment day, dual review, adversarial review, juzgar. Run blind dual review, fix confirmed issues, then re-judge.
**Path**: C:\Users\Markos\.config\opencode\skills\judgment-day\SKILL.md
**Compact Rules**:
- Only trigger on explicit user request (judgment day, dual/adversarial review, `juzgar`)
- Resolve project skills from registry; inject same `Project Standards` block into both judge and fix prompts
- Launch two blind judges in parallel with identical target and criteria — never review the code yourself
- Wait for both judges before synthesis — never accept a partial verdict
- Classify as `WARNING (real)` only if normal intended use triggers it; otherwise downgrade to INFO
- Ask before fixing Round 1 confirmed issues; after fixes, immediately re-launch both judges in parallel
- Terminal states: only `JUDGMENT: APPROVED` or `JUDGMENT: ESCALATED`
- After 2 fix iterations with remaining issues, ask user whether to continue

## Project Skills
None found.

## Project Convention Files
- AGENTS.md: (global, in ~/.config/opencode/AGENTS.md) — persona + language rules + engram protocol
- update_2.0.md: v2.0 evolution plan (architecture reference — ISP management system, Laravel 12 + Livewire 3)
