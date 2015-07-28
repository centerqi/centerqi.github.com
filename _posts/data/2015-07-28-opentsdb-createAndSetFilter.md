---
layout: post
category : data 
tags : [opentsdb]
---
{% include JB/setup %}



读读opentsdb怎么样操作hbase数据库，下面这步分是如何去构建一个查询key.

代码路径:

core/TsdbQuery.java

    /**
       * Sets the server-side regexp filter on the scanner.
       * In order to find the rows with the relevant tags, we use a
       * server-side filter that matches a regular expression on the row key.
       * @param scanner The scanner on which to add the filter.
       */
      private void createAndSetFilter(final Scanner scanner) {
        if (group_bys != null) {
          Collections.sort(group_bys, Bytes.MEMCMP);
        }
        final short name_width = tsdb.tag_names.width();
        final short value_width = tsdb.tag_values.width();
        final short tagsize = (short) (name_width + value_width);
        // Generate a regexp for our tags.  Say we have 2 tags: { 0 0 1 0 0 2 }
        // and { 4 5 6 9 8 7 }, the regexp will be:
        // "^.{7}(?:.{6})*\\Q\000\000\001\000\000\002\\E(?:.{6})*\\Q\004\005\006\011\010\007\\E(?:.{6})*$"
        final StringBuilder buf = new StringBuilder(
            15  // "^.{N}" + "(?:.{M})*" + "$"
            + ((13 + tagsize) // "(?:.{M})*\\Q" + tagsize bytes + "\\E"
               * (tags.size() + (group_bys == null ? 0 : group_bys.size() * 3))));
        // In order to avoid re-allocations, reserve a bit more w/ groups ^^^

        // Alright, let's build this regexp.  From the beginning...
        buf.append("(?s)"  // Ensure we use the DOTALL flag.
                   + "^.{")
           // ... start by skipping the metric ID and timestamp.
           .append(tsdb.metrics.width() + Const.TIMESTAMP_BYTES)
           .append("}");
        final Iterator<byte[]> tags = this.tags.iterator();
        final Iterator<byte[]> group_bys = (this.group_bys == null
                                            ? new ArrayList<byte[]>(0).iterator()
                                            : this.group_bys.iterator());
        byte[] tag = tags.hasNext() ? tags.next() : null;
        byte[] group_by = group_bys.hasNext() ? group_bys.next() : null;
        // Tags and group_bys are already sorted.  We need to put them in the
        // regexp in order by ID, which means we just merge two sorted lists.
        do {
          // Skip any number of tags.
          buf.append("(?:.{").append(tagsize).append("})*\\Q");
          if (isTagNext(name_width, tag, group_by)) {
            addId(buf, tag);
            tag = tags.hasNext() ? tags.next() : null;
          } else {  // Add a group_by.
            addId(buf, group_by);
            final byte[][] value_ids = (group_by_values == null
                                        ? null
                                        : group_by_values.get(group_by));
            if (value_ids == null) {  // We don't want any specific ID...
              buf.append(".{").append(value_width).append('}');  // Any value ID.
            } else {  // We want specific IDs.  List them: /(AAA|BBB|CCC|..)/
              buf.append("(?:");
              for (final byte[] value_id : value_ids) {
                buf.append("\\Q");
                addId(buf, value_id);
                buf.append('|');
              }
              // Replace the pipe of the last iteration.
              buf.setCharAt(buf.length() - 1, ')');
            }
            group_by = group_bys.hasNext() ? group_bys.next() : null;
          }
        } while (tag != group_by);  // Stop when they both become null.
        // Skip any number of tags before the end.
        buf.append("(?:.{").append(tagsize).append("})*$");
        scanner.setKeyRegexp(buf.toString(), CHARSET);
       }

[asynchbase](http://tsunanet.net/~tsuna/asynchbase/1.1.0/ 'http://tsunanet.net/~tsuna/asynchbase/1.1.0/')
